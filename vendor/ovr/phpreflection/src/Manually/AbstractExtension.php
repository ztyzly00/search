<?php

namespace Ovr\PHPReflection\Manually;

use Ovr\PHPReflection\FunctionReflection;
use Ovr\PHPReflection\ReflectionParameter;
use ReflectionExtension;

abstract class AbstractExtension
{
    /**
     * @abstract
     * @var string
     */
    protected $name = 'standard';

    /**
     * @var array
     */
    protected $functions = array();

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return extension_loaded($this->name);
    }

    public function getDefinedFunctions()
    {
        return get_extension_funcs($this->name);
    }

    /**
     * @return ReflectionExtension
     */
    public function getReflection()
    {
        return new ReflectionExtension($this->name);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     * @return FunctionReflection|bool
     */
    public function getFunction($name)
    {
        if (isset($this->functions[$name])) {
            $result = $this->functions[$name];

            if (count($result['parameters']) > 0) {
                foreach ($result['parameters'] as $key => $parameter) {
                    $result['parameters'][$key] = ReflectionParameter::hydrateFromArrayDefinition($result['parameters'][$key]);
                }
            }

            return new FunctionReflection(
                $name,
                $result['parameters'],
                $result['type'],
                $result['return-type'],
                isset($result['return-possible-values']) ? $result['return-possible-values'] : null
            );
        }

        return false;
    }
}
