<?php
/*
 * This file is part of the Snowcap OgoneBundle package.
 *
 * (c) Snowcap <shoot@snowcap.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Snowcap\OgoneBundle\Listener;

interface Ogone
{
    public function onOgoneSuccess($parameters);

    public function onOgoneFailure($parameters);
}
