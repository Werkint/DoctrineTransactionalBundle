<?php
namespace Werkint\Bundle\DoctrineTransactionalBundle\Service\Event;

use Werkint\Bundle\DoctrineTransactionalBundle\Service\Annotation\TransactionalInvocationContext;

/**
 * Кидается при успешном выполнении
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class TransactionalSuccessEvent extends AbstractEvent
{
    /**
     * @var mixed
     */
    protected $result;

    public function __construct(
        array $arguments,
        TransactionalInvocationContext $context,
        $result
    ) {
        $this->result = $result;

        parent::__construct($arguments, $context);
    }

    // -- Accessors ---------------------------------------

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }
}