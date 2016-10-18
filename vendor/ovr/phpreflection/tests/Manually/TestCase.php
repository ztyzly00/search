<?php

namespace Tests\Manully;

use Ovr\PHPReflection\Reflector;

abstract class TestCase extends \Tests\TestCase
{
    /**
     * @var Reflector|null
     */
    protected $reflector;

    /**
     * @return string
     */
    abstract protected function getExtensionName();

    /**
     * @return Reflector
     */
    protected function getReflector()
    {
        if ($this->reflector) {
            return $this->reflector;
        }

        return $this->reflector = new Reflector(Reflector::manuallyFactory());
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        $dataProvider = array();

        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('HHVM is not supported for testing now');
        }

        foreach (get_extension_funcs($this->getExtensionName()) as $function) {
            $dataProvider[] = array($function);
        }

        return $dataProvider;
    }

    /**
     * @dataProvider getFunctions
     *
     * @param string $functionName
     * @return bool
     */
    public function testManuallyDb($functionName)
    {
        $reflection = $this->getReflector()->getFunction($functionName);
        if ($reflection) {
            $standartFunctionReflection = new \ReflectionFunction($functionName);

            $this->assertSame($standartFunctionReflection->getNumberOfRequiredParameters(), $reflection->getNumberOfRequiredParameters());
            $this->assertSame($standartFunctionReflection->getNumberOfParameters(), $reflection->getNumberOfParameters());

            if ($reflection->getNumberOfParameters()) {
                foreach ($reflection->getParameters() as $key => $parameter) {
                    $this->assertSame($parameter, $reflection->getParameter($key));

                    $this->assertNotEmpty($parameter->getName());
                    $this->assertInternalType('integer', $parameter->getType());
                    $this->assertInternalType('boolean', $parameter->isRequired());
                }
            }

            return true;
        }

        $this->markTestSkipped('Unknown manually reflection for function: ' . $functionName);
    }
}
