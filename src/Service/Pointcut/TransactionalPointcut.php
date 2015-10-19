<?php
namespace Werkint\Bundle\DoctrineTransactionalBundle\Service\Pointcut;

use Werkint\Bundle\DoctrineTransactionalBundle\Service\Metadata\MethodMetadata;
use JMS\AopBundle\Aop\PointcutInterface;
use Metadata\MetadataFactoryInterface;

/**
 * @see    Transactional
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class TransactionalPointcut implements PointcutInterface
{
    protected $metadataFactory;

    /**
     * @param MetadataFactoryInterface $metadataFactory
     */
    public function __construct(
        MetadataFactoryInterface $metadataFactory
    ) {
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * @inheritdoc
     */
    public function matchesClass(\ReflectionClass $class)
    {
        $metadata = $this->metadataFactory->getMetadataForClass($class->getName());
        foreach ($metadata->methodMetadata as $methodMetadata) {
            if ($methodMetadata instanceof MethodMetadata) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function matchesMethod(\ReflectionMethod $method)
    {
        $metadata = $this->metadataFactory->getMetadataForClass($method->getDeclaringClass()->getName());
        foreach ($metadata->methodMetadata as $methodMetadata) {
            if ($methodMetadata instanceof MethodMetadata && $methodMetadata->name === $method->name) {
                return true;
            }
        }
        return false;
    }
}