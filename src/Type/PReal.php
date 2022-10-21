<?php

namespace Mcfedr\Plist\Type;

use Mcfedr\Plist\Exception\InvalidValueException;

class PReal implements PType
{
    /**
     * @var float
     */
    private $value;

    /**
     * @param float $value
     */
    public function __construct($value = null)
    {
        if (!is_null($value)) {
            $this->setValue($value);
        }
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param float $value
     */
    public function setValue($value)
    {
        if (!is_float($value)) {
            throw new InvalidValueException('Value not a real');
        }
        $this->value = $value;
    }
}
