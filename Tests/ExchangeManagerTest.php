<?php

namespace Ofertix\RabbitMqBundle\Tests;

use Ofertix\RabbitMqBundle\Manager\ExchangeManager;
use PhpAmqpLib\Channel\AMQPChannel;

class ExchangeManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $channel = $this->mockChannel();
        $channel
            ->expects($this->once())
            ->method('exchange_declare')
            ->with('test_exchange', 'direct', false, false, true, false, false, null, null)
        ;

        $xm = new ExchangeManager();
        $xm->setExchange('test_exchange', 'direct');
        $xm->getExchange('test_exchange', $channel);
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
