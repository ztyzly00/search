<?php

namespace Ovr\PHPReflection;

class ReflectionParameter
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var integer
     */
    protected $type;

    /**
     * @var boolean
     */
    protected $required;

    /**
     * @param integer $type
     * @param boolean $required
     * @param string $name
     */
    public function __construct($type, $required, $name = 'name')
    {
        $this->type = $type;
        $this->required = $required;
        $this->name = $name;
    }

    /**
     * @param array $definition
     * @return ReflectionParameter
     */
    public static function hydrateFromArrayDefinition(array $definition)
    {
        if (isset($definition['name'])) {
            return new self($definition['type'], $definition['required'], $definition['name']);
        }

        return new self($definition['type'], $definition['required']);
    }

    /**
     * @return mixed
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
