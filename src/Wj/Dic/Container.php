<?php

namespace Wj\Dic;


use Wj\Dic\Exception\NotFoundException;
use Wj\Dic\InstanceManager\InstanceManager;
use Wj\Dic\InstanceManager\InstanceManagerInterface;


/**
 * The Container class is a smart Dependency Injection Container.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class Container implements ContainerInterface
{
    private $parameters      = array();
    private $factories       = array();
    private $sharedFactories = array();

    private $mode;

    const NEW_INSTANCE = 1;
    const SHARE = 2;

    /**
     * @var InstanceManagerInterface
     */
    private $instanceManager;

    /**
     * @param int $mode Optional The mode (SHARE or NEW_INSTANCE), NEW_INSTANCE by default
     */
    public function __construct($mode = null)
    {
        if (null === $mode) {
            $mode = self::NEW_INSTANCE;
        }
        $this->setSharing($mode);
    }

    /**
     * @param int $mode Optional The mode (SHARE or NEW_INSTANCE), NEW_INSTANCE by default
     *
     * @throws \InvalidArgumentException if the mode isn't between 1 and 3 (which are the available modes)
     */
    public function setSharing($mode)
    {
        if ($mode > 1 || $mode < 3) {
            $this->mode = $mode;
        } else {
            throw new \InvalidArgumentException(sprintf('Mode "%s" is not available', $mode));
        }
    }

    /**
     * @return boolean
     */
    public function isSharingByDefault()
    {
        return self::SHARE === (self::SHARE & $this->mode);
    }

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
     * @throws NotFoundException if the parameter does not exists
     */
    public function getParameter($id)
    {
        if (!$this->hasParameter($id)) {
            throw new NotFoundException(sprintf('The parameter "%s" does not exists', $id));
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
    public function setFactory($id, $factory, $shared = null)
    {
        if (!is_callable($factory)) {
            throw new \InvalidArgumentException(sprintf('The factory ("%s") must be a callable', $id));
        }

        $this->factories[$id] = $factory;

        if (($this->isSharingByDefault() && false !== $shared) || $shared) {
            $this->sharedFactories[$id] = null;
        }
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
     * Checks if a specific factory is shared.
     *
     * @param string $id The identifier for the factory
     *
     * @return object|boolean The shared object, if it is shared and false otherwise
     */
    public function getSharedFactory($id)
    {
        if (isset($this->sharedFactories[$id])) {
            // it does exists
            return $this->sharedFactories[$id];
        } elseif (array_key_exists($id, $this->sharedFactories)) {
            // it is shared, but isn't initialized yet
            $this->sharedFactories[$id] = $factory = $this->getFactory($id, self::NEW_INSTANCE);

            return $factory;
        }
        return false;
    }

    /**
     * Gets a specific factory.
     *
     * @param string $id             The identifier for the factory
     * @param mixed  $flags Optional Any flags to set settings
     *
     * @return mixed The return value of the factory
     *
     * @throws NotFoundException if the factory does not exists
     */
    public function getFactory($id, $flags = null)
    {
        if (!$this->hasFactory($id)) {
            throw new NotFoundException(sprintf('The factory "%s" does not exists', $id));
        }

        if (self::NEW_INSTANCE === (self::NEW_INSTANCE & $flags)) {
            $factory = $this->factories[$id];
        } else {
            $factory = $this->getSharedFactory($id);
            if (false === $factory) {
                $factory = $this->factories[$id];
            } else {
                return $factory;
            }
        }

        return $factory($this);
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
     * {@inheritdoc}
     *
     * @throws NotFoundException if the service does not exists
     *
     * @see self::getParameter
     * @see self::getFactory
     * @see self::canCreateInstance
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
            throw new NotFoundException(sprintf('The service "%s" does not exists', $name));
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
