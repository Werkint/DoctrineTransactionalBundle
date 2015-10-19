<?php
namespace Werkint\Bundle\DoctrineTransactionalBundle\Service\Annotation;

use Doctrine\ORM\EntityManagerInterface;

/**
 * TODO: write "TransactionalInvocationContext" info
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class TransactionalInvocationContext
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;
    /**
     * @var array
     */
    protected $data;
    /**
     * @var bool
     */
    protected $preventSuccess = false;

    public function __construct(
        EntityManagerInterface $em
    ) {
        $this->em = $em;

        $this->data = [];
    }

    // -- Accessors ---------------------------------------

    /**
     * @return boolean
     */
    public function isPreventSuccess()
    {
        return $this->preventSuccess;
    }

    /**
     * @param boolean $preventSuccess
     * @return $this
     */
    public function setPreventSuccess($preventSuccess)
    {
        $this->preventSuccess = (bool)$preventSuccess;
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEm()
    {
        return $this->em;
    }
}