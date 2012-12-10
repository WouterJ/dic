<?php

namespace Wj\Dic;


use Wj\Dic\InstanceManager\InstanceManager;
use Wj\Dic\InstanceManager\InstanceManagerInterface;


/**
 * The Container class is a smart Dependency Injection Container
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class Container
{
    private $parameters = array();
    private $factories  = array();

    /**
     * @var InstanceManagerInterface
     */
    private $instanceManager;

    /**
     * @param string $id    The identifier of the parameter, e.g. mailer.transport
     * @param mixed  $value The value of the parameter, this can by any valid PHP type
     */
    public function setParameter($id, $value)
    {
        $this->parameters[$id] = $value;
    }

    /**
     * Gets all parameters for this Container.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Gets a specific parameter for this Container.
     *
     * @param string $id The idientifier for the parameter
     *
     * @return mixed The value of the parameter
     */
    public function getParameter($id)
    {
        if (!$this->hasParameter($id)) {
            throw new \LogicException(sprintf('The parameter "%s" does not exists', $id));
        }

        return $this->parameters[$id];
    }

    /**
     * @param sting $id The identifier of the parameter
     *
     * @return boolean
     */
    public function hasParameter($id)
    {
        $params = $this->getParameters();

        return isset($params[$id]) || array_key_exists($id, $params);
    }

    /**
     * @param string              $id      The identifier for the factory
     * @param string|object|array $factory The factory, this can be anything that passes the is_callable() function
     */
    public function setFactory($id, $factory)
    {
        if (!is_callable($factory)) {
            throw new \InvalidArgumentException(sprintf('The factory ("%s") must be a callable', $id));
        }

        $this->factories[$id] = $factory;
    }

    /**
     * Gets all factories in this Container.
     *
     * @return array
     */
    public function getFactories()
    {
        return $this->factories;
    }

    /**
     * Gets a specific factory.
     *
     * @param string $id the identifier for the factory
     *
     * @return mixed The return value of the factory
     */
    public function getFactory($id)
    {
        if (!$this->hasFactory($id)) {
            throw new \LogicException(sprintf('The factory "%s" does not exists', $id));
        }

        return $this->factories[$id]($this);
    }

    /**
     * @param string $id The identifier for the factory
     *
     * @return boolean
     */
    public function hasFactory($id)
    {
        $factories = $this->getFactories();

        return isset($factories[$id]) || array_key_exists($id, $factories);
    }

    /**
     * @param InstanceManagerInterface $instanceManager
     */
    public function setInstanceManager(InstanceManagerInterface $instanceManager)
    {
        $this->instanceManager = $instanceManager;
    }

    /**
     * @return InstanceManagerInterface
     */
    public function getInstanceManager()
    {
        if (null === $this->instanceManager) {
            $this->setInstanceManager(new InstanceManager());
        }

        return $this->instanceManager;
    }
}
