<?php

namespace Wj\Dic\Test;

class ContainerParameterTest extends ContainerTest
{
    /*----------------------*\
        PARAMETERS
    \*----------------------*/
    public function testParameters()
    {
        $c = $this->container;
        $c->setParameter('mailer.transport', 'sendmail');

        $this->assertTrue($c->hasParameter('mailer.transport'));
        $this->assertEquals('sendmail', $c->getParameter('mailer.transport'));
    }

    /**
     * @expectedException Wj\Dic\Exception\NotFoundException
     * @expectedExceptionMessage The parameter "foo" does not exists
     */
    public function testThrowExceptionIfParameterDoesNotExists()
    {
        $this->container->getParameter('foo');
    }
}
