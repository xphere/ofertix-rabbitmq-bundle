<?php

namespace Ofertix\RabbitMqBundle\Tests\Extension;

class ExchangeTest extends ExtensionAbstractTest
{
    public function testDefaults()
    {
        $container = $this->processConfig(array());
        $manager = $container->get('ofertix_rabbitmq.exchange_manager');

        $this->assertEquals('Ofertix\RabbitMqBundle\Manager\ExchangeManager', get_class($manager));
        $this->assertEmpty($manager->getExchangeNames());
    }
}
