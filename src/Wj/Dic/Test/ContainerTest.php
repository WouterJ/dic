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





    /*----------------------*\
        SETUP_METHODS
    \*----------------------*/
    public function setUp()
    {
        $this->container = new Container();
    }
    public function tearDown()
    {
        $this->container = null;
    }





    /*----------------------*\
        HELPERS
    \*----------------------*/
    private function notImplemented()
    {
        $this->markTestIncomplete('Not yet implemented');
    }
}
