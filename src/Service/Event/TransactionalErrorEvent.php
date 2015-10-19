<?php
namespace Werkint\Bundle\DoctrineTransactionalBundle\Service\Event;

use Werkint\Bundle\DoctrineTransactionalBundle\Service\Annotation\TransactionalInvocationContext;

/**
 * Передается при обработке ошибки
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class TransactionalErrorEvent extends AbstractEvent
{
    /**
     * @var \Exception
     */
    protected $exception;

    public function __construct(
        array $arguments,
        TransactionalInvocationContext $context,
        \Exception $exception
    ) {
        $this->exception = $exception;

        parent::__construct($arguments, $context);
    }

    // -- Accessors ---------------------------------------

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }
}