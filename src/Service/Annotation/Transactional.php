<?php
namespace Werkint\Bundle\DoctrineTransactionalBundle\Service\Annotation;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Annotation;

/**
 * Отмечает метод, который нужно выполнить в рамках одной транзакции
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 *
 * @Annotation
 * @Target("METHOD")
 */
class Transactional
{
    /**
     * @var string|null
     */
    protected $emName;
    /**
     * @var string|null
     */
    protected $onError;
    /**
     * @var string|null
     */
    protected $onSuccess;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->emName = isset($data['em']) ? $data['em'] : null;
        $this->onError = isset($data['onError']) ? $data['onError'] : null;
        $this->onSuccess = isset($data['onSuccess']) ? $data['onSuccess'] : null;
    }

    /**
     * TODO: doc
     *
     * @param array $arguments
     * @return EntityManagerInterface
     * @throws \Exception
     */
    public static function getEM(array $arguments)
    {
        return static::getContext($arguments)->getEm();
    }

    /**
     * TODO: doc
     *
     * @param array $arguments
     * @return TransactionalInvocationContext
     * @throws \Exception
     */
    public static function getContext(array $arguments)
    {
        $object = $arguments[count($arguments) - 1];
        if (!$object instanceof TransactionalInvocationContext) {
            throw new \Exception('Wrong argument type');
        }

        return $object;
    }

    // -- Accessors ---------------------------------------

    /**
     * @return null|string
     */
    public function getOnSuccess()
    {
        return $this->onSuccess;
    }

    /**
     * @return null|string
     */
    public function getOnError()
    {
        return $this->onError;
    }

    /**
     * @return null|string
     */
    public function getEmName()
    {
        return $this->emName;
    }
}