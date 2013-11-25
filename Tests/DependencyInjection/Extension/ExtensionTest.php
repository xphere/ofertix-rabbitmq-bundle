<?php

namespace Ofertix\RabbitMqBundle\Tests\DependencyInjection\Extension;

class ExtensionTest extends ExtensionAbstractTest
{
    public function testDisable()
    {
        $configuration = array(
            'enabled' => false,
        );

        $container = $this->processConfig($configuration);
        $this->assertEmpty($container->getDefinitions());
    }
}
