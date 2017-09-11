<?php

class AppKernel
{
    private $config;

    public function __construct()
    {
        $this->config = new \Clooder\DependenciesInjection\BootLoader($this);
        $this->loadConfiguration();
    }

    private function loadConfiguration()
    {
        $this->config->load();
    }

    public function getKernelRootDir(): string
    {
        return __DIR__;
    }

    public function buildCommands(): array
    {
        $commands = [];
        foreach ($this->registerCommands() as $command) {
            if (!$command instanceof \Clooder\Command\ContainerAwareConsole) {
                continue;
            }
            $command->setContainer($this->config->getContainer());
            $commands[] = $command;
        }
        return $commands;
    }

    private function registerCommands(): array
    {
        return [
            new \Clooder\Command\ProcessImageCommand(),
            new \Clooder\Command\S3TransportCommand(),
            new \Clooder\Command\SyncDirectoryToS3Command()
        ];

    }
}
