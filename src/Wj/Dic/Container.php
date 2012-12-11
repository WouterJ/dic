<?php

namespace Wj\Dic;


use Wj\Dic\InstanceManager\InstanceManager;
use Wj\Dic\InstanceManager\InstanceManagerInterface;


/**
 * The Container class is a smart Dependency Injection Container.
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
     *
     * @throws \LogicException if the parameter does not exists
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
     *
     * @throws \InvalidArgumentException if the factory isn't callable
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
     *
     * @throws \LogicException if the factory does not exists
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
        $instanceManager->setContainer($this);
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

    /**
     * Sets which arguments to use for an instance.
     *
     * @param string $name      The name of the instance
     * @param array  $arguments The arguments for the instance
     *
     * @see InstanceManagerInterface::registerInstanceArguments
     */
    public function setInstance($name, array $arguments)
    {
        $this->getInstanceManager()->registerInstanceArguments($name, $arguments);
    }

    /**
     * Gets a specific instance.
     *
     * @param string $name The name of the instance
     *
     * @return object The instance
     *
     * @see InstanceManagerInterface::getInstance
     */
    public function getInstance($name)
    {
        return $this->getInstanceManager()->getInstance($name);
    }

    /**
     * Checks if we can create an instance of a class.
     *
     * @param string $name The name of the instance
     */
    public function canCreateInstance($name)
    {
        try {
            $this->getInstance($name);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * The shortcut method for all get* methods in this class.
     *
     * @param string $name The name of the class
     *
     * @throws \RuntimeException if the service does not exists
     *
     * @see self::getParameter
     * @see self::getFactory
     */
    public function get($name)
    {
        if ($this->hasParameter($name)) {
            return $this->getParameter($name);
        } elseif ($this->hasFactory($name)) {
            return $this->getFactory($name);
        } elseif ($this->canCreateInstance($name)) {
            return $this->getInstance($name);
        } else {
            throw new \RuntimeException(
                sprintf('The "%s" service does not exists', $name)
            );
        }
    }

    /**
     * The shortcut method for all has* methods in this class.
     *
     * @param string $name The name of the class
     *
     * @see self::hasParameter
     * @see self::hasFactory
     */
    public function has($name)
    {
        return $this->hasParameter($name) || $this->hasFactory($name) || $this->canCreateInstance($name);
    }
}
