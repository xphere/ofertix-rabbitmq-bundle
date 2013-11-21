<?php

namespace Ofertix\RabbitMqBundle\Tests;

use Ofertix\RabbitMqBundle\Manager\QueueManager;
use PhpAmqpLib\Channel\AMQPChannel;

class QueueManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $channel = $this->mockChannel();
        $channel
            ->expects($this->once())
            ->method('queue_declare')
            ->with('test_queue', false, false, false, true, false, null, null)
        ;

        $xm = new QueueManager();
        $xm->setQueue('test_queue');
        $xm->getQueue('test_queue', $channel);
    }

    public function testValidQueue()
    {
        $channel = $this->mockChannel();
        $channel
            ->expects($this->once())
            ->method('queue_declare')
            ->with('test_queue', true, true, true, false, true, array(), 10)
        ;

        $xm = new QueueManager();
        $xm->setQueue('test_queue', true, true, true, false, true, array(), 10);
        $xm->getQueue('test_queue', $channel);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|AMQPChannel
     */
    protected function mockChannel()
    {
        return $this
            ->getMockBuilder('PhpAmqpLib\Channel\AMQPChannel')
                ->disableOriginalConstructor()
                ->disableOriginalClone()
            ->getMock()
        ;
    }
}
