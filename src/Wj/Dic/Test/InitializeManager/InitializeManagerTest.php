<?php

namespace Wj\Dic\Test\InitializeManager;


use Wj\Dic\Container;
use Wj\Dic\InitializeManager\InitializeManager;


class InitializeManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $manager;
    protected $container;

    public function setUp()
    {
        $this->manager = $manager = new InitializeManager();
        $manager->setContainer($this->getContainer());
    }
    public function tearDown()
    {
        $this->manager = null;
    }

    public function testInitializer()
    {
        $c = $this->container;
        $c->setInstance('Mailer', array('sendmail'));

        $m = $this->manager;
        $m->setInitializer('MailerAwareInterface', function ($class, $c) {
            $class->setMailer($c->getInstance('Mailer'));
        });

        $registration = $c->get('Registration');
        $this->assertInstanceOf('Registration', $registration);

        $m->modifyInstance($registration, 'MailerAwareInterface');
        $this->assertInstanceOf('Mailer', $registration->getMailer());
    }

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
