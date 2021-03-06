<?php

namespace Wj\Dic\InstanceManager;


use Wj\Dic\Container;


/**
 * This interface must be implemented by all Instance Managers.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
interface InstanceManagerInterface
{
    /**
     * Register arguments for a instance.
     *
     * @param string $name      The name of the instance
     * @param array  $arguments All arguments of the instance
     */
    public function registerInstanceArguments($name, array $arguments);

    /**
     * Gets a new instance.
     *
     * @param string $name The name of the instance
     *
     * @return object The instance
     */
    public function getInstance($name);

    /**
     * @param string $name The name of the instance
     *
     * @return boolean
     */
    public function hasInstance($name);

    /**
     * @param Container $container
     */
    public function setContainer(Container $container);
}
