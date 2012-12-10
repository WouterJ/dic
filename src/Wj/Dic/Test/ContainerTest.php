<?php

namespace Wj\Dic\Test;


use Wj\Dic\Container;

require_once __DIR__.'/Stubs/Mailer.php';
require_once __DIR__.'/Stubs/NewsLetter.php';


class ContainerTest extends \PHPUnit_Framework_TestCase
{
    protected $container;

    public function setUp()
    {
        $this->container = new Container();
    }
    public function tearDown()
    {
        $this->container = null;
    }

    public function testParameters()
    {
        $c = $this->container;
        $c->setParameter('mailer.transport', 'sendmail');

        $this->assertTrue($c->hasParameter('mailer.transport'));
        $this->assertEquals('sendmail', $c->getParameter('mailer.transport'));
    }

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
     */
    public function testWrongFactories()
    {
        $this->container->setFactory('mailer', 'foo');
    }

    public function testInstanceManagerWithStaticParameters()
    {
        $c = $this->container;
        $c->setInstance('Mailer', array('sendmail'));

        $mailer = $c->getInstance('Mailer');

        $this->assertInstanceOf('Mailer', $mailer);
        $this->assertEquals('sendmail', $mailer->getTransport());
    }

    public function testInstanceManagerWithObjectParameters()
    {
        $c = $this->container;
        $c->setInstance('Mailer', array('sendmail'));

        $newsletter = $c->getInstance('NewsLetter');
        $this->assertInstanceOf('NewsLetter', $newsletter);

        $mailer = $newsletter->getMailer();
        $this->assertInstanceOf('Mailer', $mailer);
        $this->assertEquals('sendmail', $mailer->getTransport());
    }

    private function notImplemented()
    {
        $this->markTestIncomplete('Not yet implemented');
    }
}
