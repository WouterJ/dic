<?php

namespace Wj\Dic\Test;

class ContainerFactoriesTest extends ContainerTest
{
    public function testFactories()
    {
        $c = $this->container;

        $c->setParameter('mailer.transport', 'sendmail');
        $c->setFactory('mailer', function ($c) {
            return new \Mailer($c->getParameter('mailer.transport'));
        });

        $this->assertTrue($c->hasFactory('mailer'));

        $mailer = $c->getFactory('mailer');
        $this->assertInstanceOf('\Mailer', $mailer);
        $this->assertEquals('sendmail', $mailer->getTransport());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The factory ("mailer") must be a callable
     */
    public function testThrowExceptionIfFactoryIsNotCallable()
    {
        $this->container->setFactory('mailer', 'foo');
    }

    /**
     * @expectedException Wj\Dic\Exception\NotFoundException
     * @expectedExceptionMessage The factory "foo" does not exists
     */
    public function testThrowExceptionIfFactoryDoesNotExists()
    {
        $this->container->getFactory('foo');
    }
}
