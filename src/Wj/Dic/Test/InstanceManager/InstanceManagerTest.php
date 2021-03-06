<?php

namespace Wj\Dic\Test\InstanceManager;


use Wj\Dic\Container;
use Wj\Dic\InstanceManager\InstanceManager;

require_once __DIR__.'/../Stubs/Mailer.php';
require_once __DIR__.'/../Stubs/NewsLetter.php';


class InstanceManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $manager;
    protected $container;

    public function setUp()
    {
        $this->manager = $manager = new InstanceManager();
        $manager->setContainer($this->getContainer());
        $this->getContainer()->setInstanceManager($manager);
    }
    public function tearDown()
    {
        $this->manager = null;
    }





    /*----------------------*\
        INSTANCES
    \*----------------------*/
    public function testInstancesWithStaticParameters()
    {
        $m = $this->manager;
        $m->registerInstanceArguments('Mailer', array('sendmail'));

        $mailer = $m->getInstance('Mailer');
        $this->assertInstanceOf('Mailer', $mailer);
        $this->assertEquals('sendmail', $mailer->getTransport());
    }

    public function testInstancesWithServiceParameters()
    {
        $m = $this->manager;
        $m->registerInstanceArguments('Mailer', array('sendmail'));

        $newsletter = $m->getInstance('NewsLetter');
        $this->assertInstanceOf('NewsLetter', $newsletter);

        $mailer = $newsletter->getMailer();
        $this->assertInstanceOf('Mailer', $mailer);
        $this->assertEquals('sendmail', $mailer->getTransport());
    }

    public function testInstanceWithServiceStaticParameters()
    {
        $m = $this->manager;
        $m->registerInstanceArguments('Mailer', array('sendmail'));
        $m->registerInstanceArguments('NewsLetter', array('@Mailer'));

        $newsletter = $m->getInstance('NewsLetter');
        $this->assertInstanceOf('NewsLetter', $newsletter);

        $mailer = $newsletter->getMailer();
        $this->assertInstanceOf('Mailer', $mailer);
        $this->assertEquals('sendmail', $mailer->getTransport());
    }

    public function testInstancesWithParameterParameters()
    {
        $this->getContainer()->setParameter('mailer.transport', 'sendmail');
        $m = $this->manager;
        $m->registerInstanceArguments('Mailer', array('%mailer.transport%'));

        $mailer = $m->getInstance('Mailer');
        $this->assertInstanceOf('Mailer', $mailer);
        $this->assertEquals('sendmail', $mailer->getTransport());
    }





    /*----------------------*\
        SHARING
    \*----------------------*/
    public function testInstancesAreNotShared()
    {
        $m = $this->manager;
        $m->registerInstanceArguments('Mailer', array('sendmail'));

        $newsletter = $m->getInstance('NewsLetter');
        $newsletter1 = $m->getInstance('NewsLetter');
        $this->assertNotSame($newsletter, $newsletter1);

        $this->assertNotSame($newsletter->getMailer(), $newsletter1->getMailer());
    }





    /*----------------------*\
        EXCEPTIONS
    \*----------------------*/
    /**
     * @expectedException        Wj\Dic\Exception\InstanceManager\CouldNotInitializeException
     * @expectedExceptionMessage Could not initialize the "Foo" class; the class does not exists
     */
    public function testThrowExceptionIfClassDoesNotExists()
    {
        $this->manager->getInstance('Foo');
    }

    /**
     * @expectedException Wj\Dic\Exception\InstanceManager\CouldNotInitializeException
     * @expectedExceptionMessage Could not initialize the "Mailer" class; the constructor has 1 required parameters, 0 given
     */
    public function testThrowExceptionIfItGetsToLessArguments()
    {
        $m = $this->manager;
        $m->registerInstanceArguments('Mailer', array());

        $m->getInstance('Mailer');
    }





    /*----------------------*\
        SHARING
    \*----------------------*/
    protected function setContainer($container)
    {
        $this->container = $container;
    }

    protected function getContainer()
    {
        if (null === $this->container) {
            $this->setContainer(new Container());
        }

        return $this->container;
    }
}
