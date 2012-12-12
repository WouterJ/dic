<?php

namespace Wj\dic\InitializeManager;


use Wj\Dic\Container;


class InitializeManager implements InitializeManagerInterface
{
    /**
     * @var Container
     */
    private $container;

    private $initializers = array();

    /**
     * {@inheritdoc}
     */
    public function setInitializer($interface, $factory)
    {
        if (!is_callable($factory)) {
            throw new \InvalidArgumentException(
                sprintf('The initializer ("%s") must be a PHP callable.', $interface)
            );
        }

        $this->initializers[$interface] = $factory;
    }

    /**
     * @return array
     */
    public function getInitializers()
    {
        return $this->initializers;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyInstance($instance, $interface)
    {
        if (isset($this->initializers[$interface])) {
            $this->initializers[$interface]($instance, $this->getContainer());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setUpInstance($instance)
    {
        $reflection = new \ReflectionClass($instance);
        $interfaces = $reflection->getInterfaceNames();

        foreach ($interfaces as $interface) {
            $this->modifyInstance($instance, $interface);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }
}
