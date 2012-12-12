Een introductie voor WjDic
==========================

Bedankt dat je de tijd hebt genomen om je verder te verdiepen in WjDic. Deze
introductie zal zo'n 10 minuten kosten om te lezen en daarna ben je klaar om
WjDic te gebruiken in je projecten.

Wat is WjDic?
-------------

WjDic is een Smart Dependency Injection Container. Doormiddel van een Depedency
Injection Container kun je heel makkelijk werken met services en classes. Een
introductie tot Dependency Injection ga ik hier niet plaatsen, die kun je ergens
anders lezen, bijv. in `de Symfony Documentatie`_.

Onze Mailer service
-------------------

We hebben in onze applicatie een ``Mailer`` service nodig. Deze maken we door
gewoon een Mailer class te maken:

.. code-block:: php

    class Mailer
    {
        private $transport;

        public function __construct()
        {
            $this->transport = 'sendmail';
        }
    }

De service gebruiken
~~~~~~~~~~~~~~~~~~~~

Om een service te registreren moet je eerst `WjDic downloaden`_. Hierna kunnen
we gewoon een nieuwe instance aanmaken van de ``Wj\Dic\Container`` class, welke
de container voorstelt:

.. code-block:: php

    // ...
    $container = new \Wj\Dic\Container();

Nu moeten we onze service registreren. Een WjDic Container zonder uitbereiding
bevat 2 verschillende typen services: Parameters en Factories. De parameters
houden gewoon bepaalde waarden vast, terwijl de factories functies zijn die een
object/service kunnen aanmaken. In ons geval gebruiken we een factory om onze
service te registreren:

.. code-block:: php

    // ...
    $container->setFactory('mailer', function ($c) {
        return new Mailer();
    });

Deze method bevat 2 argumenten: De naam van de service (``'mailer'``) en de
functie die hem aanmaakt, deze functie krijgt als argument de Service Container
mee.

Vervolgens kunnen we op elke plek in onze applicatie de mailer service
aanroepen, we krijgen dan een nieuwe instance van onze Mailer class:

.. code-block:: php

    // ...
    $mailer = $container->get('mailer');

Parameters gebruiken
--------------------

Onze service is momenteel nog helemaal niet flexibel. Wat als we een ander
transport willen gebruiken? Om hem wat flexibeler te maken voegen we dat in als
een parameter:

.. code-block:: php

    class Mailer
    {
        // ...
        public function __construct($transport)
        {
            $this->transport = $transport;
        }
    }

Vervolgens passen we onze factory aan om deze parameter in te vullen:

.. code-block:: php

    // ...
    $container->setFactory('mailer', function ($c) {
        return new Mailer($c->get('mailer.transport'));
    });

We zien hier dat we de service ``mailer.transport`` aanvragen, laten we die ook
gaan maken voordat we deze mailer aanroepen:

.. code-block:: php

    // ...
    $container->setParameter('mailer.transport', 'sendmail');

    // zelfde als eerst, maar dan wat flexibeler
    $mailer = $container->get('mailer');

    // ... ergens verderop in je code
    $container->setParameter('mailer.transport', 'phpmail');
    $mailer = $container->get('mailer'); // de mailer met de phpmail transport

Werken met instances, het stukje *'smart'*
------------------------------------------

Dan nu de feature waarom deze Service Container een 'Smart Dependency Injection
Container' genoemd wordt, en geen normale DI container.

In ons geval doen we niet heel veel vreemdst met onze service, we bepalen alleen
welke argumenten de constructor meekrijgt. In dat geval kun je ook gebruik maken
van de InstanceManager die samen met Wj\Dic meekomt. Met de InstanceManager vul
je in welke argumenten je wilt voor de Mailer instance en hij regelt het voor
je.

.. caution::

    Waar bij services de eerste parameter een fictieve naam mag zijn is het bij
    instances van belang dat de naam gelijk is aan de class naam.

Om de mailer services te gebruiken stellen we in welke parameters we willen
gebruiken:

.. code-block:: php

    // ...
    $container->setInstance('Mailer', array('sendmail'));

Het 2e argument van de ``setInstance`` method is een array van argumenten die
aan de constructor worden meegegeven. We kunnen de Mailer instance nu gewoon
opvragen:

.. code-block:: php

    // ...
    // geeft dezelfde uitkomst als in de service voorbeelden van hierboven
    $mailer = $container->get('Mailer');

Services als argumenten
-----------------------

DI is officieel het meegeven van andere services aan een nieuwe services. Je
kunt verwijzen naar een andere service door de service naam te prefixen met een
``@``.

Stel dat we nu een ``NewsLetter`` class hebben die de ``Mailer`` service als
argument heeft:

.. code-block:: php

    class NewsLetter
    {
        private $mailer;

        public function __construct(Mailer $mailer)
        {
            $this->mailer = $mailer;
        }
    }

Nu kunnen we naar de ``Mailer`` service verwijzen in onze arguments:

.. code-block:: php

    // ...
    $container->setInstance('Mailer', array('sendmail'));
    $container->setInstance('NewsLetter', array('@Mailer'));

    $newsletter = $container->get('NewsLetter');

De service raden
~~~~~~~~~~~~~~~~

Maar dat is nog niet alles, onze InstanceManager is slimmer. Doordat we in de
constructor van ``NewsLetter`` aangegeven hebben dat we een ``Mailer`` class
willen als eerste argument zal onze InstanceManager opzoek gaan naar deze
instance en hem proberen aan te maken. We hoeven dus alleen nog maar de 
argumenten in te stellen voor het ``$transport`` argument van onze ``Mailer``
service en voor de rest kan de Container alles krijgen:

.. code-block:: php

    // ...
    $container->setInstance('Mailer', array('sendmail');

    // zelfde als in het voorbeeld hierboven
    $newsletter = $container->get('NewsLetter');

Conclusie
---------

En bij deze vorm van magie eindigt deze tutorial. Je hebt gelezen hoe je van een
hele simpele parameter/factory structuur kunt eindigen in een nog simpelere vorm
door de Container alles te laten raden.

1 onderdeel van WjDic hebben we nog niet besproken, dat is het aanpassen van een
object aan de hand van bepaalde interfaces. In "`Aanpassen met Interfaces`_" kun
je hier meer over lezen.

.. _`de Symfony Documentatie`: http://symfony.com/doc/current/book/service_container.html
.. _`WjDic downloaden`: downloaden.rst
.. _`Aanpassen met Interfaces`: interfaces.rst
