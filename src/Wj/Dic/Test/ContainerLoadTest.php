<?php

namespace Wj\Dic\Test;


use Wj\Dic\Container;


class ContainerLoadTest extends ContainerTest
{
    public function testLoadContainer()
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
}
