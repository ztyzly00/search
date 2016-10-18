<?php

namespace Ovr\PHPReflection\Manually\PHP70;

use Ovr\PHPReflection\FunctionReflection;
use Ovr\PHPReflection\Manually\AbstractExtension;
use Ovr\PHPReflection\Types;

class JSON extends AbstractExtension
{
    protected $name = 'json';

    /**
     * @var array
     */
    protected $functions = array(
        'json_decode' => array(
            'type' => FunctionReflection::TYPE_UNDEFINED,
            'return-type' => Types::MIXED,
            'parameters' => array(
                array(
                    'type' => Types::INT_TYPE,
                    'required' => true
                ),
                array(
                    'type' => Types::BOOLEAN_TYPE,
                    'required' => false,
                    'default' => false
                ),
                array(
                    'type' => Types::INT_TYPE,
                    'required' => false,
                    'default' => 512
                ),
                array(
                    'type' => Types::INT_TYPE,
                    'required' => false,
                    'default' => 0
                )
            )
        ),
        'json_encode' => array(
            'type' => FunctionReflection::TYPE_UNDEFINED,
            'return-type' => Types::STRING_TYPE,
            'parameters' => array(
                array(
                    'type' => Types::MIXED,
                    'required' => true,
                ),
                array(
                    'type' => Types::INT_TYPE,
                    'required' => false,
                    'default' => 0
                ),
                array(
                    'type' => Types::INT_TYPE,
                    'required' => false,
                    'default' => 512
                )
            )
        ),
        'json_last_error_msg' => array(
            'type' => FunctionReflection::TYPE_UNDEFINED,
            'return-type' => Types::STRING_TYPE,
            'parameters' => array()
        ),
        'json_last_error' => array(
            'type' => FunctionReflection::TYPE_UNDEFINED,
            'return-type' => Types::INT_TYPE,
            'parameters' => array()
        )
    );
}
