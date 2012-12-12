<?php

namespace Wj\Dic\Test;

class ContainerShortcutTest extends ContainerTest
{
    public function testShortCutMethodWithParameters()
    {
        $c = $this->container;
        $c->setParameter('mailer.transport', 'sendmail');

        $this->assertTrue($c->has('mailer.transport'));
        $this->assertEquals('sendmail', $c->get('mailer.transport'));
    }

    public function testShortCutMethodWithFactories()
    {
        $c = $this->container;
        $c->setParameter('mailer.transport', 'sendmail');

        $c->setFactory('mailer', function ($c) {
            return new \Mailer($c->get('mailer.transport'));
        });

        $mailer = $c->get('mailer');
        $this->assertInstanceOf('Mailer', $mailer);

        $transport = $mailer->getTransport();
        $this->assertEquals('sendmail', $transport);
    }

    public function testShortCutMethodWithInstances()
    {
        $c = $this->container;
        $c->setInstance('Mailer', array('sendmail'));

        $newsletter = $c->get('NewsLetter');
        $this->assertInstanceOf('NewsLetter', $newsletter);

        $mailer = $newsletter->getMailer();
        $this->assertInstanceOf('Mailer', $mailer);
        $this->assertEquals('sendmail', $mailer->getTransport());
    }

    /**
     * @expectedException Wj\Dic\Exception\NotFoundException
     * @expectedExceptionMessage The service "foo" does not exists
     */
    public function testThrowExceptionIfServiceDoesNotExists()
    {
        $this->container->get('foo');
    }

    public function testHasserWithParameters()
    {
        $c = $this->container;

        $this->assertFalse($c->has('foo'));
        $c->setParameter('foo', 'bar');
        $this->assertTrue($c->has('foo'));
    }

    public function testHasserWithFactories()
    {
        $c = $this->container;

        $this->assertFalse($c->has('mailer'));
        $c->setFactory('mailer', function () {
            return new Mailer('sendmail');
        });
        $this->assertTrue($c->has('mailer'));
    }

    public function testHasserWithInstances()
    {
        $c = $this->container;

        $this->assertFalse($c->has('Mailer'));
        $c->setInstance('Mailer', array('sendmail'));
        $this->assertTrue($c->has('Mailer'));
    }
}
