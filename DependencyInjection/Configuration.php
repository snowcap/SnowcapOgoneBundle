<?php
/*
 * This file is part of the Snowcap OgoneBundle package.
 *
 * (c) Snowcap <shoot@snowcap.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Snowcap\OgoneBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('snowcap_ogone');

        $rootNode
            ->children()
            ->scalarNode('pspid')->end()
            ->scalarNode('environment')->end()
            ->scalarNode('sha_in')->end()
            ->scalarNode('sha_out')->end()
            ->arrayNode('options')
            ->useAttributeAsKey('key')
            ->prototype('variable')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
