<?php
namespace Werkint\Bundle\DoctrineTransactionalBundle\Service\Metadata;

use Doctrine\Common\Annotations\Reader;
use Werkint\Bundle\DoctrineTransactionalBundle\Service\Annotation\Transactional;
use Metadata\Driver\DriverInterface;
use Metadata\MergeableClassMetadata;

/**
 * Драйвер аннотаций
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class AnnotationDriver implements
    DriverInterface
{
    protected $reader;

    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @inheritdoc
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $classMetadata = new MergeableClassMetadata($class->getName());

        foreach ($class->getMethods() as $method) {
            $annotations = $this->reader->getMethodAnnotations($method);

            foreach ($annotations as $annotation) {
                if ($annotation instanceof Transactional) {
                    $propertyMetadata = new MethodMetadata($class->getName(), $method->getName());
                    $propertyMetadata
                        ->setEm($annotation->getEmName())
                        ->setOnError($annotation->getOnError())
                        ->setOnSuccess($annotation->getOnSuccess());
                    $classMetadata->addMethodMetadata($propertyMetadata);
                }
            }
        }

        return $classMetadata;
    }
}