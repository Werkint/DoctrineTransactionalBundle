<?php
namespace Werkint\Bundle\DoctrineTransactionalBundle\Service\Metadata;

use Metadata\MethodMetadata as BaseMethodMetadata;

/**
 * TODO: write "MethodMetadata" info
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class MethodMetadata extends BaseMethodMetadata
{
    /**
     * @var string|null
     */
    protected $onError;
    /**
     * @var string|null
     */
    protected $onSuccess;
    /**
     * @var string|null
     */
    protected $em;

    // -- Accessors ---------------------------------------

    /**
     * @return null|string
     */
    public function getOnError()
    {
        return $this->onError;
    }

    /**
     * @param null|string $onError
     * @return $this
     */
    public function setOnError($onError)
    {
        $this->onError = $onError;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getOnSuccess()
    {
        return $this->onSuccess;
    }

    /**
     * @param null|string $onSuccess
     * @return $this
     */
    public function setOnSuccess($onSuccess)
    {
        $this->onSuccess = $onSuccess;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * @param null|string $em
     * @return $this
     */
    public function setEm($em)
    {
        $this->em = $em;
        return $this;
    }
}