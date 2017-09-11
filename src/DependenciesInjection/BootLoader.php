<?php

namespace Clooder\DependenciesInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class BootLoader
{
    private $container;
    private $kernel;
    private $loader;

    public function __construct(\AppKernel $kernel)
    {
        $this->kernel = $kernel;

        $this->container = new \Symfony\Component\DependencyInjection\ContainerBuilder();
    }

    public function getContainer()
    {
        if ($this->loader === null) {
            $this->load();
        }
        if (!$this->container->isCompiled()) {
            $this->container->compile();
        }
        return $this->container;

    }

    public function load()
    {
        $this->registerExtension();

        $loader = new YamlFileLoader($this->container,
            new FileLocator($this->kernel->getKernelRootDir() . '/../config')
        );
        $loader->load('config.yml');
        $this->loader = $loader;
    }

    private function registerExtension()
    {
        $this->container->registerExtension(new AppExtension());
    }
}
