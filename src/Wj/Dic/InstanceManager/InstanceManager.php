<?php

namespace Wj\Dic\InstanceManager;


use Wj\Dic\Container;


/**
 * The basic implementation of the InstanceManagerInterface
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class InstanceManager implements InstanceManagerInterface
{
    /**
     * @var Container
     */
    private $container;

    private $instances = array();

    /**
     * {@inheritdoc}
     */
    public function registerInstanceArguments($name, array $arguments)
    {
        $this->instances[$name] = $arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstance($name)
    {
        if (!class_exists($name)) {
            throw new \RuntimeException(
                sprintf('We are not able to initialize the "%s" class, as it does not exists', $name)
            );
        }

        $reflection = new \ReflectionClass($name);
        $constructor = $reflection->getConstructor();

        if (null === $constructor) {
            // no constructor
            return $reflection->newInstance();
        }

        if (isset($this->instances[$name])) {
            $arguments = $this->instances[$name];
        } else {
            $arguments = $this->getArgumentsFromClassName($name);
        }

        return $reflection->newInstanceArgs($arguments);
    }

    /**
     * @param string $name The name of the class
     *
     * @return array The parameters
     */
    protected function getArgumentsFromClassName($name)
    {
        $reflection = new \ReflectionClass($name);
        $constructor = $reflection->getConstructor();

        if (null === $constructor) {
            // no constructor, do nothing
            return;
        }
        
        $requiredParams = $constructor->getNumberOfRequiredParameters();
        if (0 === $requiredParams) {
            // no required parameters, return an empty array
            return array();
        } else {
            // required parameters, check if we can find a service that match
            $parameters = $constructor->getParameters();
            $arguments = array();

            foreach ($parameters as $parameter) {
                $class = $parameter->getClass();

                if (null !== $class) {
                    // todo: change getInstance into a more verbose get method
                    $arguments[] = $this->getContainer()->getInstance($class->getName());
                    continue;
                } else {
                    throw new \LogicException(
                        sprintf('Could not initalize the "%s" class, it has undefined parameters', $name)
                    );
                }

                throw new \LogicException(
                    sprintf('Could not initialize the "%s" class, it has undefined services as parameters', $name)
                );
            }

            return $arguments;
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
    protected function getContainer()
    {
        return $this->container;
    }
}
