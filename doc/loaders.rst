Werken met configuratie
=======================

Om alle instellingen direct in OO PHP te schrijven is niet overzichtelijk.
Daarom biedt WjDic een manier om configuraties in te laden.

Je kan de ``load()`` method gebruiken om configuratie in te laden. Deze method
verwacht devolgende array (eventuele lege items kunnen weggelaten worden):

.. code-block:: php

    array(
        ['parameters'] => array(
            'mailer.transport' => 'sendmail',
            // ... overige parameters
        ),
        ['factories']  => array(
            'mailer' => array('MailerFactory', 'createMailer'),
            // ... overige factories
        ),
        ['instances']  => array(
            'Mailer' => array('%mailer.transport%'),
            // ... overige instances
        ),
        ['initializers' => array(
            'Registration' => array('RegistrationInitializer', 'initialize'),
            // ... overige initializers
        )
    )

WjDic zal deze array omzetten naar een Container en deze inladen in de huidige
container.

Container in Container
----------------------

Soms kom je wel in situaties waarbij je 2 service containers hebt, je krijgt
bijvoorbeeld een service container van een bundle/module en je hebt een global
service container. Nu wil je die 2 combineren tot 1 global service container.

Ook dit kan de ``loader`` functie aan. Importeer gewoon de ene container in de
ander en WjDic zal alle services overnemen:

.. code-block:: php

    use Wj\Dic\Container;

    $globalContainer = new Container();

    $bundleContainer = new Container();
    $bundleContainer->setParameter('mailer.transport', 'sendmail');
    $bundleContainer->setFactory('mailer', function ($c) {
        return new Mailer($c->get('mailer.transport'));
    });

    $globalContainer->load($bundleContainer);

    $globalContainer->has('mailer'); // true

Mochten de services in allebei de containers voorkomen zal de container die je
importeerd voorrang krijgen op de andere container. Dit kun je aanpassen door
de ``Container::NO_OVERRIDE`` flag als 2e argument mee te geven aan de ``load``
method.

Extra tips
----------

Classes voor functies
~~~~~~~~~~~~~~~~~~~~~

De functies plaats je over het algemeen niet in je configuratie file. Als
argument voor een factory of initializer verwacht WjDic een PHP callable. Dit
kan dus ook een method zijn uit een class. In ons geval bijv:

.. code-block:: php

    use Wj\Dic\Container;

    class MailerFactory
    {
        public static function createMailer(Container $container)
        {
            return new \Mailer($container->get('mailer.parameter'));
        }
    }

Parsing
~~~~~~~

Hoe je die array krijgt maakt WjDic niks uit. De simpelste methode is om een PHP
array in te laden met bijv. ``require``. Maar je kan ook andere formats
gebruiken en die parsen met bijv. `Zend\Config\Reader`_ of `Symfony\Yaml`_.
