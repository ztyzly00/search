<?php

namespace Ovr\PHPReflection;

use RuntimeException;

class FunctionReflection
{
    const TYPE_UNDEFINED = 0;

    /**
     * It's can be exec in runtime because it's safe
     */
    const TYPE_RUNNABLE = 1;

    /**
     * Is debugging function
     */
    const TYPE_DEBUGGING = 2;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var ReflectionParameter[]
     */
    protected $parameters;

    /**
     * @var int|null
     */
    protected $returnType;

    /**
     * @var int
     */
    protected $type = self::TYPE_UNDEFINED;

    /**
     * @var int|null
     */
    protected $returnPossibleValue;

    public function __construct($name, array $parameters, $type = FunctionReflection::TYPE_UNDEFINED, $returnType = null, $returnPossibleValue = null)
    {
        $this->name = $name;
        $this->parameters = $parameters;
        $this->returnType = $returnType;
        $this->returnPossibleValue = $returnPossibleValue;
    }

    /**
     * Gets number of parameters
     *
     * @return int
     */
    public function getNumberOfParameters()
    {
        return count($this->parameters);
    }

    /**
     * Run function and return result of it
     *
     * @param array $parameters
     * @return mixed
     */
    public function run(array $parameters = array())
    {
        if (count($parameters) < $this->getNumberOfRequiredParameters()) {
            throw new RuntimeException("It's not possible to run function '{$this->name}' via count of parameters < requiredParameters");
        }

        if (!$this->isRunnable()) {
            throw new RuntimeException("It's not possible to run function '{$this->name}' because it's not runnable via type");
        }

        return call_user_func_array($this->name, $parameters);
    }

    /**
     * @return int
     */
    public function isRunnable()
    {
        return $this->type = self::TYPE_RUNNABLE;
    }

    /**
     * Gets number of required parameters
     *
     * @return int
     */
    public function getNumberOfRequiredParameters()
    {
        if (count($this->parameters) == 0) {
            return 0;
        }

        $count = 0;

        foreach ($this->parameters as $parameter) {
            if ($parameter->isRequired()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        $isInDisabledFunctions = false !== strpos(ini_get('disable_functions'), $this->name);

        return !($isInDisabledFunctions);
    }

    /**
     * @return int|null
     */
    public function getReturnType()
    {
        return $this->returnType;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return ReflectionParameter[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return ReflectionParameter|boolean
     */
    public function getParameter($key)
    {
        return $this->parameters[$key];
    }
}
