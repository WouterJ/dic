<?php

namespace Wj\Dic\Test;


use Wj\Dic\Container;


class ContainerLoadTest extends ContainerTest
{
    public function testLoadContainerWithFactories()
    {
        $c = $this->container;
        $c1 = new Container();
        $c1->setParameter('mailer.transport', 'sendmail');
        $c1->setFactory('mailer', function ($c1) {
            return new \Mailer($c1->get('mailer.transport'));
        });

        $c->load($c1);

        $mailer = $c->get('mailer');
        $this->assertInstanceOf('Mailer', $mailer);
        $this->assertEquals('sendmail', $mailer->getTransport());
    }

    public function testLoadContainerWithInstances()
    {
        $c = $this->container;
        $c1 = new Container();
        $c1->setInstance('Mailer', array('sendmail'));

        $c->load($c1);

        $mailer = $c->get('Mailer');
        $this->assertInstanceOf('Mailer', $mailer);
        $this->assertEquals('sendmail', $mailer->getTransport());
    }

    public function testLoadContainerWithInitializers()
    {
        $c = $this->container;
        $c->setInstance('Mailer', array('sendmail'));

        $c1 = new Container();
        $c1->setInitializer('MailerAwareInterface', function ($instance, $c1) {
            $instance->setMailer($c1->get('Mailer'));
        });

        $c->load($c1);

        $registration = $c->get('Registration');
        $this->assertInstanceOf('Registration', $registration);
        $this->assertInstanceOf('Mailer', $registration->getMailer());
    }

    public function testLoadConfigWithFactories()
    {
        $c = $this->container;
        $c->load(array(
            'parameters' => array(
                'mailer.transport' => 'sendmail',
            ),
            'factories' => array(
                'mailer' => function ($c1) {
                    return new \Mailer($c1->get('mailer.transport'));
                },
            ),
        ));

        $mailer = $c->get('mailer');
        $this->assertInstanceOf('Mailer', $mailer);
        $this->assertEquals('sendmail', $mailer->getTransport());
    }

    public function testLoadConfigWithInstances()
    {
        $c = $this->container;
        $c->load(array(
            'instances' => array(
                'Mailer' => array('sendmail'),
            ),
        ));

        $mailer = $c->get('Mailer');
        $this->assertInstanceOf('Mailer', $mailer);
        $this->assertEquals('sendmail', $mailer->getTransport());
    }

    public function testLoadConfigWithInitializers()
    {
        $c = $this->container;
        $c->load(array(
            'instances' => array(
                'Mailer' => array('sendmail'),
            ),
            'initializers' => array(
                'MailerAwareInterface' => function ($instance, $c1) {
                    $instance->setMailer($c1->get('Mailer'));
                },
            ),
        ));

        $registration = $c->get('Registration');
        $this->assertInstanceOf('Registration', $registration);
        $this->assertInstanceOf('Mailer', $registration->getMailer());
    }
}
