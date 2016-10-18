<?php

namespace Ovr\PHPReflection\Manually\PHP56;

use Ovr\PHPReflection\Manually\CoreInterface;

class Core implements CoreInterface
{
    protected $extensions;

    public function __construct()
    {
        $this->extensions = new \SplObjectStorage();
        $this->extensions->attach(new Standard());
        $this->extensions->attach(new JSON());
    }

    public function getFunction($name)
    {
        /** @var Standard $ext */
        foreach ($this->extensions as $ext) {
            $result = $ext->getFunction($name);
            if ($result) {
                return $result;
            }
        }

        return false;
    }
}
