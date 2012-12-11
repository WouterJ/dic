<?php

namespace Wj\Dic\InstanceManager;


use Wj\Dic\Container;


/**
 * The basic implementation of the InstanceManagerInterface.
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
     *
     * @throws \RunTimeException if the class does not exists
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
            $arguments = $this->getArgumentsFromParameters($name, $this->instances[$name]);
        } else {
            $this->instances[$name] = $this->getArgumentsFromClassName($name);
            $arguments = $this->getArgumentsFromParameters($name, $this->instances[$name]);
        }

        return $reflection->newInstanceArgs($arguments);
    }

    /**
     * Creates arguments from the predefined parameters.
     *
     * @param array $parameters All predefined parameters for the constructor
     *
     * @return array The arguments
     *
     * @throws \RuntimeException if the are more required parameters than the parameters given
     */
    protected function getArgumentsFromParameters($name, array $parameters)
    {
        $reflection = new \ReflectionClass($name);
        $requiredParams = $reflection->getConstructor()->getNumberOfRequiredParameters();

        if (count($parameters) < $requiredParams) {
            throw new \RuntimeException(
                sprintf(
                    'Could not initalize the "%s" class, there are %d required parameters, %d given', 
                    $name, $requiredParams, count($parameters)
                )
            );
        }

        $self = $this;
        $arguments = array_map(function ($item) use ($self) {
            if ('@' == substr($item, 0, 1)) {
                return $self->getContainer()->get(
                    substr($item, 1)
                );
            }
            return $item;
        }, $parameters);

        return $arguments;
    }

    /**
     * @param string $name The name of the class
     *
     * @return array The arguments
     *
     * @throws \LogicException if the constructor has undefined static arguments
     * @throws \LogicException if the constructor has undefined services in the arguments
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
                    $arguments[] = '@'.$class->getName();
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
    public function getContainer()
    {
        return $this->container;
    }
}
