<?php


namespace Clooder\DependenciesInjection;


use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $nodeRoot = $treeBuilder->root('app');

        $this->setImageProcessor($nodeRoot);
        $this->setS3Client($nodeRoot);
        return $treeBuilder;
    }


    private function setImageProcessor(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('image_processor')
            ->children()
            ->scalarNode('driver')->isRequired()->validate()
            ->ifNotInArray(['imagick', 'gd'])
            ->thenInvalid('Invalid image driver %s')
            ->end()
            ->end()
            ->arrayNode('profiles')
            ->prototype('array')
            ->prototype('array')
            ->children()
            ->scalarNode('width')->isRequired()->end()
            ->scalarNode('height')->isRequired()->defaultValue(null)->end()
            ->scalarNode('quality')->isRequired()->end()
            ->end()
            ->end()
            ->end()
            ->end();
    }

    private function setS3Client(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('s3_client')
            ->children()
            ->arrayNode('credentials')
            ->children()
            ->scalarNode('key')->isRequired()->end()
            ->scalarNode('secret')->isRequired()->end()
            ->end()->end()
            ->scalarNode('bucket_name')->isRequired()->end()
            ->scalarNode('region')->isRequired()->end()
            ->scalarNode('version')->isRequired()->end();
    }
}
