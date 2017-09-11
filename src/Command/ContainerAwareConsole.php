<?php

namespace Clooder\Command;

abstract class ContainerAwareConsole extends \Symfony\Component\Console\Command\Command
{
    private $container;

    public function getContainer(): \Symfony\Component\DependencyInjection\ContainerInterface
    {
        return $this->container;
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }
}
