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
