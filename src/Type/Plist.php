<?php

namespace Mcfedr\Plist\Type;

class Plist
{
    /**
     * @var PRoot|null
     */
    private $value;

    /**
     * @param PRoot $value
     */
    public function __construct(PRoot $value = null)
    {
        $this->value = $value;
    }

    /**
     * @return PRoot|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param PRoot $value
     */
    public function setValue(PRoot $value = null)
    {
        $this->value = $value;
    }
}
