<?php


namespace Clooder\DependenciesInjection;


use Aws\S3\S3Client;
use Intervention\Image\ImageManager;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use Clooder\Processor\ImageProcessor;
use Clooder\Profile\Image;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

class AppExtension extends Extension
{

    /**
     * Loads a specific configuration.
     *
     * @param array $configs An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $this->addImageManager($config, $container);
        $this->addImage($config, $container);
        $this->addS3Client($config, $container);
    }

    private function addImageManager(array $config, ContainerBuilder $builder)
    {
        $driver = $config['image_processor']['driver'];
        $builder->register('image.manager', ImageManager::class)
            ->setAutowired(false)
            ->setArguments([
                ["driver" => $driver]
            ])
            ->setPublic(false);
    }


    private function addImage(array $config, ContainerBuilder $container)
    {
        $container->register('image.processor', ImageProcessor::class)
            ->setArguments([
                new Reference('image.manager')
            ])
            ->setPublic(false);
        $container->register('image', Image::class)
            ->setArguments([
                $config['image_processor']['profiles'],
                new Reference('image.processor')
            ]);
    }

    private function addS3Client(array $config, ContainerBuilder $container)
    {

        $s3clientConfig = $config['s3_client'];
        $container->register('s3.client', S3Client::class)
            ->setArguments([
                [
                    'credentials' => $s3clientConfig['credentials'],
                    'version'=> $s3clientConfig['version'],
                    'region'=> $s3clientConfig['region']
                ]
            ])
            ->setPublic(false);

        $container->register('s3.adapter', AwsS3Adapter::class)
            ->setArguments([
                new Reference('s3.client'),
                $s3clientConfig['bucket_name']
            ])
            ->setPublic(false);

        $container->register('league.fs', Filesystem::class)
            ->setArguments([
                new Reference('s3.adapter')
            ])
            ->setPublic(false);
        $container->register('mount', MountManager::class)
            ->setArguments([
                [
                    's3' => new Reference('league.fs')
                ]
            ]);

    }
}
