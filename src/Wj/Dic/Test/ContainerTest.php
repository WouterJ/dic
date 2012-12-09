<?php

namespace Wj\Dic\Test;


use Wj\Dic\Container;

require_once __DIR__.'/Stubs/Mailer.php';


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
        $this->assertInstanceOf('\Mailer', $c->getFactory('mailer'));
    }
}