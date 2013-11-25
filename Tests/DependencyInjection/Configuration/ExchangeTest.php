<?php

namespace Ofertix\RabbitMqBundle\Tests\DependencyInjection\Configuration;

class ExchangeTest extends ConfigurationAbstractTest
{
    /** @dataProvider providerValidExchanges */
    public function testValidExchanges(array $config, array $expected = null)
    {
        $result = $this->processConfig($config);

        $this->assertArrayHasKey('exchanges', $result);
        if (null !== $expected) {
            $this->assertEquals($expected, $result['exchanges']);
        }
    }

    public function providerValidExchanges()
    {
        return array(
            'empty by default' => array(
                array(), array(),
            ),
            'empty when null' => array(
                array('exchanges' => null, ), array(),
            ),
            'one exchange with default values' => array(
                array('exchanges' => array(
                    'default_exchange' => null,
                )),
                array(
                    'default_exchange' => array(
                        'type' => 'direct',
                        'passive' => false,
                        'durable' => false,
                        'auto_delete' => true,
                        'internal' => false,
                        'nowait' => false,
                        'arguments' => null,
                        'ticket' => null,
                    )
                ),
            ),
            'exchange valid types' => array(
                array(
                    'exchanges' => array(
                        'direct' => array('type' => 'direct', ),
                        'fanout' => array('type' => 'fanout', ),
                        'topic' => array('type' => 'topic', ),
                        'headers' => array('type' => 'headers', ),
                    )
                )
            )
        );
    }

    /** @dataProvider providerInvalidExchanges */
    public function testInvalidExchanges(array $config, $exceptionName)
    {
        $this->setExpectedException($exceptionName);
        $this->processConfig($config);
    }

    public function providerInvalidExchanges()
    {
        return array(
            'invalid type of exchange' => array(
                array(
                    'exchanges' => array(
                        'invalid_type' => array('type' => 'thiswillneverbeavalidtype', ),
                    )
                ),
                'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException',
            ),
        );
    }
}
