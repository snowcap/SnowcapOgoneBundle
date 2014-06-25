Introduction
============

This bundle introduces the Ogone payment process gateway into a Symfony project
It uses the marlon-ogone Bundle, and adds a different integration approach

More info about Ogone can be found here: http://www.ogone.com

WARNING: THIS BUNDLE IS IN BETA STAGE, USE AT YOUR OWN RISKS !

Prerequisites
============

* Symfony 2.1>=
* You must have a valid Ogone account, configured with SHA-IN and SHA-OUT security activated


Installation
============

Download the bundle:

```js
{
"require": {
    "snowcap/ogone-bundle": "dev-master"
}
```

``` bash
$ php composer.phar update snowcap/ogone-bundle
```

Add it to your application's kernel:

``` php
// app/ApplicationKernel.php
public function registerBundles()
{
    return array(
        // ...
        new Snowcap\OgoneBundle\SnowcapOgoneBundle(),
        // ...
    );
}
```

Configuration
============

Put the following configuration options in your config file:

``` yaml
snowcap_ogone:
    pspid: [your_ogone_pspid]
    environment: [test|prod]
    sha_in: [your_ogone_sha_in_passhprase]
    sha_out: [your_ogone_sha_out_passhprase]
    options:
        # any option you may want to pass to Ogone, as key: value pairs
```

Usage
============

Getting the Ogone form to use in your view
------------

A service 'snowcap_ogone.manager' allows you to get the ogone form rendering, whereby you can also define the acceptUrl, and any other option you want to send to Ogone
An example could be:

``` php
/** @var $ogone \Snowcap\OgoneBundle\Manager */
$ogone = $this->get('snowcap_ogone');

$ogoneForm = $ogone->getRequestForm($locale, $orderId, $customerName, $amount, $currency, array(
    'acceptUrl' => $this->generateUrl('your_success_page_route_name', array(), true),
    // and any other option your may want to pass to Ogone
));

return array(
    'ogone_form' => $ogoneForm,
);
```

Pay attention, this is not a Symfony form, just a simple rendered form provided by the Ogone library.

``` twig
...
{{ ogone_form|raw }}

```

Getting Ogone result
-------------

To catch Ogone's result, you have to create a service and tag it as an event subscriber (or an event listener):
For example:

``` yaml
my_company_bundle.ogone_subscriber:
    class: MyCompany\MyBundle\Ogone\OgoneSubscriber
    tags:
        - { name: kernel.event_subscriber }
```

That service has to implement the EventSubscriberInterface, like the following:

``` php
<?php
namespace MyCompany\MyBundle\Ogone;
    
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Snowcap\OgoneBundle\Event\OgoneEvent;
use Snowcap\OgoneBundle\OgoneEvents;

class OgoneSubscriber implements EventSubscriberInterface
{
    /**
     * @param \Snowcap\OgoneBundle\Event\OgoneEvent $event
     */
    public function onOgoneSuccess(OgoneEvent $event)
    {
        
    }

    /**
     * @param \Snowcap\OgoneBundle\Event\OgoneEvent $event
     */
    public function onOgoneError(OgoneEvent $event)
    {
        
    }
    
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            OgoneEvents::SUCCESS => 'onOgoneSuccess',
            OgoneEvents::ERROR => 'onOgoneError'
        );
    }
}
```

You now have two methods inside your bundle to operate all the business logic you need, enjoy !
