<?php

namespace Wj\Dic\InitializeManager;


use Wj\Dic\Container;


/**
 * This interface must be implemented by all Initialize Managers.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
interface InitializeManagerInterface
{
    /**
     * Sets an initializer for a specific interface.
     *
     * @param string   $interface The name of the interface
     * @param callable $factory   A PHP callable to modify the instance
     */
    public function setInitializer($interface, $factory);

    /**
     * @param string $interface The name of the interface
     *
     * @return boolean
     */
    public function hasInitializer($interface);

    /**
     * Modify an instance with a initializer.
     *
     * @param object $instance  The instance to modify
     * @param string $interface The name of the interface
     */
    public function modifyInstance($instance, $interface);

    /**
     * Gets an instance and checks if it implements some interfaces.
     *
     * @param object $instance The instance to modify
     */
    public function setUpInstance($instance);

    /**
     * @param Container $container
     */
    public function setContainer(Container $container);
}
