<?php
/*
 * This file is part of the Snowcap OgoneBundle package.
 *
 * (c) Snowcap <shoot@snowcap.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Snowcap\OgoneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/ogone")
 */
class DefaultController extends Controller
{

    /**
     * @Route("")
     */
    public function indexAction()
    {
        $parameters = array_merge($this->getRequest()->query->all(),$this->getRequest()->request->all());

        /** @var $ogone \Snowcap\OgoneBundle\Manager */
        $ogone = $this->get('snowcap_ogone');

        $ogone->paymentResponse($parameters);

        return new \Symfony\Component\HttpFoundation\Response('ok');
    }
}