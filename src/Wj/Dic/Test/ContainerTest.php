<?php

namespace Wj\Dic\Test;


use Wj\Dic\Container;


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
}
