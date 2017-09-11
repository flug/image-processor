<?php


namespace Clooder\Command;


use League\Flysystem\AdapterInterface;
use League\Flysystem\MountManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class S3TransportCommand extends ContainerAwareConsole
{
    protected function configure()
    {
        $this->setName('s3:transport')
            ->addOption('directoryWatch', 'd', InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        /** @var MountManager $fs */
        $fs = $container->get('mount');
        $directoryWatch = $input->getOption('directoryWatch');


        $container->get('file.notify')->loop($directoryWatch, function ($buffer) use ($fs, $directoryWatch, $output) {
            $pathFile = substr($buffer['path'], strlen($directoryWatch) + 1);
            if (!file_exists($buffer['path']) || is_dir($buffer['path'])) {
                return;
            }

            $pattern = 's3://' . $pathFile;
            $contents = file_get_contents($buffer['path']);
            if ($fs->has($pattern)) {
                $fs->put($pattern, $contents, [AdapterInterface::VISIBILITY_PUBLIC]);
                $message = 'Update file : ' . $pathFile;
            } else {

                $fs->write($pattern, $contents, [AdapterInterface::VISIBILITY_PUBLIC]);
                $message = 'Create file : ' . $pathFile;
            }
            $output->writeln($message);
        });
    }

}
