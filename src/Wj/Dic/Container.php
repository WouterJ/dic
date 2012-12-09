<?php

namespace Wj\Dic;

/**
 * The Container class is a smart Dependency Injection Container
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class Container
{
    private $parameters = array();

    /**
     * @param string $name  The name of the parameter, e.g. mailer.transport
     * @param mixed  $value The value of the parameter, this can by any valid PHP type
     */
    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;
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
     * @return mixed The value of the parameter
     */
    public function getParameter($name)
    {
        if (!$this->hasParameter($name)) {
            throw new \LogicException(sprintf('The parameter "%s" does not exists', $name));
        }

        return $this->parameters[$name];
    }

    /**
     * Checks if the Container has a specific parameter
     *
     * @param sting $name The name of the parameter
     *
     * @return boolean
     */
    public function hasParameter($name)
    {
        $params = $this->getParameters();

        return isset($params[$name]) || array_key_exists($name, $params);
    }
}
