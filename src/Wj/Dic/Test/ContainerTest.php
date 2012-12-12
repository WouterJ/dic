<?php

namespace Wj\Dic\Test;


use Wj\Dic\Container;

require_once __DIR__.'/Stubs/Mailer.php';
require_once __DIR__.'/Stubs/NewsLetter.php';


/**
 * @covers Container
 */
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






    /*----------------------*\
        SHORTCUT
    \*----------------------*/
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





    /*----------------------*\
        SHARING
    \*----------------------*/
    public function testNotSharingByDefault()
    {
        $c = $this->container;
        $c->setInstance('Mailer', array('sendmail'));

        $this->assertNotSame($c->get('Mailer'), $c->get('Mailer'));
    }

    public function testSharingServiceSpecific()
    {
        $c = $this->container;
        $c->setFactory('Mailer', function ($c) {
            return new \Mailer('sendmail');
        }, true);

        $this->assertSame($c->get('Mailer'), $c->get('Mailer'));
    }

    public function testSharingOption()
    {
        $c = $this->container;
        $c->setSharing(Container::SHARE);
        $c->setFactory('Mailer', function ($c) {
            return new \Mailer('sendmail');
        });

        $this->assertSame($c->getFactory('Mailer'), $c->getFactory('Mailer'));
    }

    public function testSharingOptionAgainstSpecificSharing()
    {
        $c = $this->container;
        $c->setSharing(Container::SHARE);
        $c->setFactory('Mailer', function ($c) {
            return new \Mailer('sendmail');
        }, false);

        $this->assertNotSame($c->getFactory('Mailer'), $c->getFactory('Mailer'));
    }





    /*----------------------*\
        HELPERS
    \*----------------------*/
    private function notImplemented()
    {
        $this->markTestIncomplete('Not yet implemented');
    }
}
