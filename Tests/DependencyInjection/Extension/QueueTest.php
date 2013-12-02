<?php

namespace Ofertix\RabbitMqBundle\Tests\DependencyInjection\Extension;

class QueueTest extends ExtensionAbstractTest
{
    public function testDefinedQueues()
    {
        $container = $this->processConfig(array(
            'queues' => array(
                'first' => null,
                'second' => null,
            ),
        ));

        $this->assertNotNull($container->getParameter('ofertix_rabbitmq.queue.first'));
        $this->assertNotNull($container->getParameter('ofertix_rabbitmq.queue.second'));
    }
}
