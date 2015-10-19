<?php
namespace Werkint\Bundle\DoctrineTransactionalBundle\Service\Event;

use Doctrine\ORM\EntityManagerInterface;
use Werkint\Bundle\DoctrineTransactionalBundle\Service\Annotation\TransactionalInvocationContext;

/**
 * Базовый класс события
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
abstract class AbstractEvent
{
    /**
     * @var array|mixed[]
     */
    protected $arguments;
    /**
     * @var TransactionalInvocationContext
     */
    protected $context;

    public function __construct(
        array $arguments,
        TransactionalInvocationContext $context
    ) {
        $this->arguments = $arguments;
        $this->context = $context;
    }

    /**
     * @param string $argument
     * @return mixed
     */
    public function getArgument($argument)
    {
        return $this->arguments[$argument];
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEm()
    {
        return $this->context->getEm();
    }

    // -- Accessors ---------------------------------------

    /**
     * @return array|mixed[]
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @return TransactionalInvocationContext
     */
    public function getContext()
    {
        return $this->context;
    }
}