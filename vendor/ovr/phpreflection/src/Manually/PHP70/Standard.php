<?php

namespace Ovr\PHPReflection\Manually\PHP70;

use Ovr\PHPReflection\FunctionReflection;
use Ovr\PHPReflection\Manually\AbstractExtension;
use Ovr\PHPReflection\Types;

class Standard extends AbstractExtension
{
    protected $name = 'standard';

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * @var array
     */
    protected $functions = array(
        'constant' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::MIXED,
            'parameters' => array(
                array(
                    'type' => Types::STRING_TYPE,
                    'required' => true,
                    'name' => 'name'
                )
            )
        ),
        'bin2hex' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::STRING_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::STRING_TYPE,
                    'required' => true,
                    'name' => 'str'
                )
            )
        ),
        'hex2bin' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::STRING_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::STRING_TYPE,
                    'required' => true,
                    'name' => 'str'
                )
            )
        ),
        'sleep' => array(
            'type' => FunctionReflection::TYPE_UNDEFINED,
            'return-type' => Types::INT_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::INT_TYPE,
                    'required' => true,
                    'name' => 'seconds'
                )
            )
        ),
        'usleep' => array(
            'type' => FunctionReflection::TYPE_UNDEFINED,
            'return-type' => Types::VOID_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::INT_TYPE,
                    'required' => true,
                    'name' => 'micro_seconds'
                )
            )
        ),
        'gettype' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::MIXED,
            'return-possible-values' => array(
                'boolean',
                'integer',
                'double',
                'string',
                'array',
                'object',
                'resource',
                'NULL',
                'unknown type'
            ),
            'parameters' => array(
                array(
                    'type' => Types::INT_TYPE,
                    'required' => true
                )
            )
        ),
        'intdiv' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::INT_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::INT_TYPE,
                    'name' => 'numerator',
                    'required' => true
                ),
                array(
                    'type' => Types::INT_TYPE,
                    'name' => 'divisor',
                    'required' => true
                )
            )
        ),
        'intval' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::INT_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::MIXED,
                    'required' => true,
                    'name' => 'var'
                ),
                array(
                    'type' => Types::INT_TYPE,
                    'required' => false,
                    'name' => 'var',
                    'default' => 10
                )
            )
        ),
        'doubleval' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::DOUBLE_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::MIXED,
                    'required' => true
                )
            )
        ),
        'floatval' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::DOUBLE_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::MIXED,
                    'required' => true
                )
            )
        ),
        'boolval' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::BOOLEAN_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::MIXED,
                    'required' => true
                )
            )
        ),
        'is_array' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::BOOLEAN_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::MIXED,
                    'required' => true
                )
            )
        ),
        'is_bool' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::BOOLEAN_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::MIXED,
                    'required' => true
                )
            )
        ),
        'is_double' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::BOOLEAN_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::MIXED,
                    'required' => true
                )
            )
        ),
        'is_float' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::BOOLEAN_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::MIXED,
                    'required' => true
                )
            )
        ),
        'is_int' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::BOOLEAN_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::MIXED,
                    'required' => true
                )
            )
        ),
        'is_integer' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::BOOLEAN_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::MIXED,
                    'required' => true
                )
            )
        ),
        'is_long' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::BOOLEAN_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::MIXED,
                    'required' => true
                )
            )
        ),
        'is_null' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::BOOLEAN_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::MIXED,
                    'required' => true
                )
            )
        ),
        'count' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::INT_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::MIXED,
                    'required' => true,
                    'name' => 'array_or_countable'
                ),
                array(
                    'type' => Types::INT_TYPE,
                    'required' => false,
                    'name' => 'mode',
                    'default' => COUNT_NORMAL
                )
            )
        ),
        'sizeof' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::INT_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::MIXED,
                    'required' => true,
                    'name' => 'array_or_countable'
                ),
                array(
                    'type' => Types::INT_TYPE,
                    'required' => false,
                    'name' => 'mode',
                    'default' => COUNT_NORMAL
                )
            )
        ),
        'abs' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::NUMBER,
            'parameters' => array(
                array(
                    'type' => Types::MIXED,
                    'required' => true,
                    'name' => 'number'
                )
            )
        ),
        'acos' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::DOUBLE_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::DOUBLE_TYPE,
                    'required' => true,
                    'name' => 'arg'
                )
            )
        ),
        'acosh' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::DOUBLE_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::DOUBLE_TYPE,
                    'required' => true,
                    'name' => 'arg'
                )
            )
        ),
        'asin' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::DOUBLE_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::DOUBLE_TYPE,
                    'required' => true,
                    'name' => 'arg'
                )
            )
        ),
        'asinh' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::DOUBLE_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::DOUBLE_TYPE,
                    'required' => true,
                    'name' => 'arg'
                )
            )
        ),
        'cos' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::DOUBLE_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::DOUBLE_TYPE,
                    'required' => true,
                    'name' => 'arg'
                )
            )
        ),
        'sin' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::DOUBLE_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::DOUBLE_TYPE,
                    'required' => true,
                    'name' => 'arg'
                )
            )
        ),
        'sqrt' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::DOUBLE_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::DOUBLE_TYPE,
                    'required' => true,
                    'name' => 'arg'
                )
            )
        ),
        'is_nan' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::BOOLEAN_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::DOUBLE_TYPE,
                    'required' => true,
                    'name' => 'val'
                )
            )
        ),
        'is_infinite' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::BOOLEAN_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::DOUBLE_TYPE,
                    'required' => true,
                    'name' => 'val'
                )
            )
        ),
        'is_finite' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::BOOLEAN_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::DOUBLE_TYPE,
                    'required' => true,
                    'name' => 'val'
                )
            )
        ),
        'cli_set_process_title' => array(
            'type' => FunctionReflection::TYPE_UNDEFINED,
            'return-type' => Types::BOOLEAN_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::STRING_TYPE,
                    'required' => true,
                    'name' => 'title'
                )
            )
        ),
        'rand' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::INT_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::INT_TYPE,
                    'required' => false,
                    'name' => 'min'
                ),
                array(
                    'type' => Types::INT_TYPE,
                    'required' => false,
                    'name' => 'max'
                )
            )
        ),
        'mt_rand' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::INT_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::INT_TYPE,
                    'required' => false,
                    'name' => 'min'
                ),
                array(
                    'type' => Types::INT_TYPE,
                    'required' => false,
                    'name' => 'max'
                )
            )
        ),
        'getrandmax' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::INT_TYPE,
            'parameters' => array()
        ),
        'mt_getrandmax' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::INT_TYPE,
            'parameters' => array()
        ),
        'srand' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::VOID_TYPE,
            'parameters' => array(
                array(
                    'name' => 'seed',
                    'type' => Types::INT_TYPE,
                    'required' => false,
                    'default' => null
                )
            )
        ),
        'mt_srand' => array(
            'type' => FunctionReflection::TYPE_RUNNABLE,
            'return-type' => Types::VOID_TYPE,
            'parameters' => array(
                array(
                    'name' => 'seed',
                    'type' => Types::INT_TYPE,
                    'required' => false,
                    'default' => null
                )
            )
        ),
        'cli_get_process_title' => array(
            'type' => FunctionReflection::TYPE_UNDEFINED,
            'return-type' => Types::STRING_TYPE,
            'parameters' => array()
        ),
    );
}
