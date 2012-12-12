<?php

namespace Wj\Dic\Test;

class ContainerInstancesTest extends ContainerTest
{
    public function testInstanceManagerWithStaticParameters()
    {
        $c = $this->container;
        $c->setInstance('Mailer', array('sendmail'));

        $mailer = $c->getInstance('Mailer');

        $this->assertInstanceOf('Mailer', $mailer);
        $this->assertEquals('sendmail', $mailer->getTransport());
    }
}
