<?php

namespace Mcfedr\Plist\Type;

use Mcfedr\Plist\Exception\InvalidValueException;

class PReal implements PType
{
    /**
     * @var float|null
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
     * @return float|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param float|null $value
     */
    public function setValue($value)
    {
        if (!is_real($value)) {
            throw new InvalidValueException('Value not a real');
        }
        $this->value = $value;
    }
}
