<?php

namespace Ofertix\RabbitMqBundle\Tests\Extension;

class QueueTest extends ExtensionAbstractTest
{
    public function testDefaults()
    {
        $container = $this->processConfig(array());
        $manager = $container->get('ofertix_rabbitmq.queue_manager');

        $this->assertEquals('Ofertix\RabbitMqBundle\Manager\QueueManager', get_class($manager));
        $this->assertEmpty($manager->getQueueNames());
    }

    public function testDefinedQueues()
    {
        $container = $this->processConfig(array(
            'queues' => array(
                'first' => null,
                'second' => null,
            ),
        ));
        $manager = $container->get('ofertix_rabbitmq.queue_manager');

        $this->assertEquals('Ofertix\RabbitMqBundle\Manager\QueueManager', get_class($manager));
        $this->assertEquals(array('first', 'second', ), $manager->getQueueNames());
    }
}
