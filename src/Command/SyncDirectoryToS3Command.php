<?php


namespace Clooder\Command;


use League\Flysystem\AdapterInterface;
use League\Flysystem\MountManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class SyncDirectoryToS3Command extends ContainerAwareConsole
{

    protected function configure()
    {
        $this->setName('sync:directory')
            ->addOption('directory', 'd', InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $directory = $input->getOption('directory');

        $container = $this->getContainer();

        /** @var MountManager $fs */
        $fs = $container->get('mount');

        $finder = new Finder();
        /** @var SplFileInfo $file */
        foreach ($finder->in($directory)->files() as $file) {
            $pathFile = $file->getRelativePathname();
            $pathName = $file->getPathname();

            $pattern = 's3://' . $pathFile;
            $contents = file_get_contents($pathName);
            if ($fs->has($pattern)) {
                $fs->put($pattern, $contents, [AdapterInterface::VISIBILITY_PUBLIC]);
                $message = 'Update file : ' . $pathFile;
            } else {

                $fs->write($pattern, $contents, [AdapterInterface::VISIBILITY_PUBLIC]);
                $message = 'Create file : ' . $pathFile;
            }
            $output->writeln($message);
        }

    }
}
