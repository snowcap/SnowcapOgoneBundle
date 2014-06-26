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
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/ogone")
 */
class DefaultController extends Controller
{

    /**
     * @Route("")
     */
    public function indexAction(Request $request)
    {
        $parameters = array_merge($request->query->all(), $request->request->all());

        /** @var $ogone \Snowcap\OgoneBundle\OgoneManager */
        $ogone = $this->get('snowcap_ogone');

        $ogone->paymentResponse($parameters);

        return new \Symfony\Component\HttpFoundation\Response();
    }
}