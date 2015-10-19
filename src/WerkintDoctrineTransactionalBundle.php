<?php
namespace Werkint\Bundle\DoctrineTransactionalBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * WerkintDoctrineTransactionalBundle.
 */
class WerkintDoctrineTransactionalBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }
}
