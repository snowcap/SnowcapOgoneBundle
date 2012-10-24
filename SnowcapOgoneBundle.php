<?php
/*
 * This file is part of the Snowcap OgoneBundle package.
 *
 * (c) Snowcap <shoot@snowcap.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Snowcap\OgoneBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Snowcap\OgoneBundle\DependencyInjection\Compiler\ListenerCompilerPass;

class SnowcapOgoneBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ListenerCompilerPass());
    }
}
