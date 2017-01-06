<?php

namespace Andvab\GeonamesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Andvab\GeonamesBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('andvab_geonames');

        $rootNode
            ->children()
                ->variableNode('feature_classes')
                    ->defaultValue(array('A', 'H', 'L', 'P', 'T', 'U', 'V'))
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
