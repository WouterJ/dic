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
