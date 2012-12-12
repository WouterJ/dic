<?php

namespace Wj\Dic\InstanceManager;


use Wj\Dic\Container;
use Wj\Dic\Exception\InstanceManager\CouldNotInitializeException;


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
     * @throws CouldNotInitializeException if the class does not exists
     */
    public function getInstance($name)
    {
        if (!class_exists($name)) {
            throw $this->generateInitializeException($name, 'the class does not exists');
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
     * @throws CouldNotInitializeException if there are more required parameters than the parameters given
     */
    protected function getArgumentsFromParameters($name, array $parameters)
    {
        $reflection = new \ReflectionClass($name);
        $requiredParams = $reflection->getConstructor()->getNumberOfRequiredParameters();

        if (count($parameters) < $requiredParams) {
            throw $this->generateInitializeException($name, 
                sprintf(
                    'the constructor has %d required parameters, %d given',
                    $requiredParams, count($parameters)
                )
            );
        }

        $self = $this;
        $arguments = array_map(function ($item) use ($self) {
            if ('@' == substr($item, 0, 1)) {
                return $self->getContainer()->get(substr($item, 1));
            } elseif ('%' == substr($item, 0, 1)) {
                preg_match('|%(.*?)%|', $item, $data);
                list(, $parameter) = $data;

                return $self->getContainer()->getParameter($parameter);
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
     * @throws CouldNotInitializeException if the constructor has undefined static arguments
     * @throws CouldNotInitializeException if the constructor has undefined services in the arguments
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
                    throw $this->generateInitializeException($name, 'it has undefined parameters');
                }

                throw $this->generateInitializeException($name, 'it has undefined services as parameters');
            }

            return $arguments;
        }
    }

    /**
     * @return array
     */
    public function getInstances()
    {
        return $this->instances;
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

    /**
     * Generates a new exception.
     *
     * @param string $name    The name of the class
     * @param string $message The describtion why we couldn't initialize the class
     *
     * @return CouldNotInitializeException
     */
    private function generateInitializeException($name, $message = null)
    {
        if (null !== $message) {
            $message = '; '.trim($message);
        }

        return new CouldNotInitializeException(
            sprintf(
                'Could not initialize the "%s" class%s',
                $name, $message
            )
        );
    }
}
