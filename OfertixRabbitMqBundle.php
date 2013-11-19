<?php

namespace Ofertix\RabbitMqBundle;

use Ofertix\RabbitMqBundle\DependencyInjection as DI;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OfertixRabbitMqBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new DI\OfertixRabbitMqExtension();
    }
}
