<?php
namespace Werkint\Bundle\DoctrineTransactionalBundle\Service\Pointcut;

use CG\Proxy\MethodInterceptorInterface;
use CG\Proxy\MethodInvocation;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Werkint\Bundle\DoctrineTransactionalBundle\Service\Annotation\TransactionalInvocationContext;
use Werkint\Bundle\DoctrineTransactionalBundle\Service\Event\AbstractEvent;
use Werkint\Bundle\DoctrineTransactionalBundle\Service\Event\TransactionalErrorEvent;
use Werkint\Bundle\DoctrineTransactionalBundle\Service\Event\TransactionalSuccessEvent;
use Werkint\Bundle\DoctrineTransactionalBundle\Service\Metadata\MethodMetadata;
use Metadata\MetadataFactoryInterface;

/**
 * Проверяет операцию на наличие кошельков
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class PointcutInterceptor implements
    MethodInterceptorInterface
{
    protected $metadataFactory;
    protected $doctrine;

    /**
     * @param MetadataFactoryInterface $metadataFactory
     * @param Registry                 $doctrine
     */
    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        Registry $doctrine
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->doctrine = $doctrine;
    }

    /**
     * @param string $class
     * @param string $method
     * @throws \Exception
     * @return MethodMetadata|null
     */
    protected function findMethodMetadata($class, $method)
    {
        $metadata = $this->metadataFactory->getMetadataForClass($class);
        foreach ($metadata->methodMetadata as $methodMetadata) {
            /** @var MethodMetadata $methodMetadata */
            if ($methodMetadata->name !== $method) {
                continue;
            }

            if ($methodMetadata instanceof MethodMetadata) {
                return $methodMetadata;
            }
            break;
        }

        throw new \Exception('Wrong class specified: ' . $class);
    }

    /**
     * @inheritdoc
     */
    public function intercept(MethodInvocation $invocation)
    {
        $metadata = $this->findMethodMetadata(
            get_class($invocation->object),
            $invocation->reflection->name
        );

        $em = $this->doctrine->getManager($metadata->getEm());
        if (!$em instanceof EntityManagerInterface) {
            throw new \Exception('Wrong object manager class');
        }
        $context = new TransactionalInvocationContext($em);

        $arguments = [];
        foreach ($invocation->reflection->getParameters() as $i => $param) {
            $arguments[$param->name] = $invocation->arguments[$i];
        }

        try {
            $em->beginTransaction();
            /** @see https://www.percona.com/blog/2012/08/28/differences-between-read-committed-and-repeatable-read-transaction-isolation-levels/ */
            /** @see http://www.ovaistariq.net/597/understanding-innodb-transaction-isolation-levels/ */
            $em->getConnection()->setTransactionIsolation(
                Connection::TRANSACTION_READ_COMMITTED
            );

            array_push($invocation->arguments, $context);
            $ret = $invocation->proceed();

            $em->flush();

            if (!$em->isOpen() || $em->getConnection()->isRollbackOnly()) {
                throw new \Exception('Rollback invoked');
            }

            $em->commit();

            if ($metadata->getOnSuccess() && !$context->isPreventSuccess()) {
                $this->invokeEvent(
                    $invocation->object,
                    $metadata->getOnSuccess(),
                    new TransactionalSuccessEvent($arguments, $context, $ret)
                );
            }

            return $ret;
        } catch (\Exception $e) {
            if ($em->getConnection()->isTransactionActive()) {
                // TODO: postgresql, savepoints
                if (!$e instanceof DBALException && !$e instanceof \PDOException && $em->isOpen() && !$em->getConnection()->isRollbackOnly()) {
                    try {
                        $em->flush();
                    } catch (\Exception $e) {
                        // TODO: log? stop catching?
                    }
                }
                $em->rollback();
            }

            if (!$em->isOpen()) {
                $this->doctrine->resetManager($metadata->getEm());
                $contextOld = $context;
                /** @var EntityManagerInterface $em */
                $em = $this->doctrine->getManager($metadata->getEm());
                $context = new TransactionalInvocationContext($em);
                $context->setData($contextOld->getData());
            }

            if ($metadata->getOnError()) {
                return $this->invokeEvent(
                    $invocation->object,
                    $metadata->getOnError(),
                    new TransactionalErrorEvent($arguments, $context, $e)
                );
            }

            throw $e;
        }
    }

    protected function invokeEvent($object, $method, AbstractEvent $event)
    {
        $reflection = new \ReflectionMethod(get_class($object), $method);
        $reflection->setAccessible(true);
        try {
            $ret = $reflection->invoke($object, $event);
            $reflection->setAccessible(false);
            return $ret;
        } catch (\Exception $e) {
            $reflection->setAccessible(false);
            throw $e;
        }
    }
}