<?php

namespace Wj\Dic\Test;

class ContainerInitializerTest extends ContainerTest
{
    public function testInitializer()
    {
        $c = $this->container;
        $c->setInstance('Mailer', array('sendmail'));

        $c->setInitializer('MailerAwareInterface', function ($class, $c) {
            $class->setMailer($c->getInstance('Mailer'));
        });

        $registration = $c->get('Registration');
        $this->assertInstanceOf('Registration', $registration);

        $this->assertInstanceOf('Mailer', $registration->getMailer());
    }
}
