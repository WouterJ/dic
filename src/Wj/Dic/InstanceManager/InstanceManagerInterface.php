<?php

namespace Wj\Dic\InstanceManager;

/**
 * This interface must be implemented by all Instance Managers
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
interface InstanceManagerInterface
{
    /**
     * Register parameters for a instance
     *
     * @param string $name       The name of the instance
     * @param array  $parameters All parameters of the instance
     */
    public function registerInstanceParameters($name, array $parameters);

    /**
     * Gets a new instance
     *
     * @param string $name The name of the instance
     *
     * @return object The instance
     */
    public function getInstance($name);
}
