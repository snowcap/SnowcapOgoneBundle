<?php
/*
 * This file is part of the Snowcap OgoneBundle package.
 *
 * (c) Snowcap <shoot@snowcap.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Snowcap\OgoneBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ListenerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('snowcap_ogone.manager')) {
            return;
        }

        $definition = $container->getDefinition('snowcap_ogone.manager');

        foreach ($container->findTaggedServiceIds('snowcap_ogone.listener') as $id => $attributes) {
            $definition->addMethodCall('addListener', array(new Reference($id)));
        }
    }
}