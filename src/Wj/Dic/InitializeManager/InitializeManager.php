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
