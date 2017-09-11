<?php

namespace Clooder\Command;

use Clooder\Processor\ImageProcessor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessImageCommand extends ContainerAwareConsole
{
    protected function configure()
    {
        $this->setName('image:process')
            ->addOption('path', 'f', InputOption::VALUE_REQUIRED)
            ->addOption('profile', 'p', InputOption::VALUE_REQUIRED)
            ->addOption('outputDirectory', 'o', InputOption::VALUE_REQUIRED);

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getOption('path');
        $profile = $input->getOption('profile');
        $outputDirectory = $input->getOption('outputDirectory');


        $container = $this->getContainer();

        /** @var ImageProcessor[] $processors */
        $processors = $container->get('image')->get($profile);
        /** @var ImageProcessor $processor */
        foreach ($processors as $processor) {
            $processor->setImage($path)->setOutputDirectory($outputDirectory)->process();
        };

    }
}
