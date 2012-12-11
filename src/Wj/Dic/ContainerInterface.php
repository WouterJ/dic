<?php

namespace Wj\Dic;

/**
 * This interface is implemented by every Dependency Injection Container.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
interface ContainerInterface
{
    /**
     * Gets a service.
     *
     * @param string $name The name of the class
     */
    public function get($name);
}
