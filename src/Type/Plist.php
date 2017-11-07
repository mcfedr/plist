<?php

namespace Mcfedr\Plist\Type;

class Plist
{
    /**
     * @var PType
     */
    private $value;

    /**
     * @param PType $value
     */
    public function __construct(PType $value = null)
    {
        $this->value = $value;
    }

    /**
     * @return PType
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param PType $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}
