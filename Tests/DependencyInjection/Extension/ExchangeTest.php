<?php

namespace Ofertix\RabbitMqBundle\Tests\DependencyInjection\Extension;

class ExchangeTest extends ExtensionAbstractTest
{
    public function testDefinedExchanges()
    {
        $container = $this->processConfig(array(
            'exchanges' => array(
                'first' => null,
                'second' => null,
            ),
        ));

        $this->assertNotNull($container->getParameter('ofertix_rabbitmq.exchange.first'));
        $this->assertNotNull($container->getParameter('ofertix_rabbitmq.exchange.second'));
    }
}
