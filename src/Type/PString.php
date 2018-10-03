<?php

namespace Mcfedr\Plist\Type;

use Mcfedr\Plist\Exception\InvalidValueException;

class PString implements PType
{
    /**
     * @var string|null
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct($value = null)
    {
        if (!is_null($value)) {
            $this->setValue($value);
        }
    }

    /**
     * @return string|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string|null $value
     */
    public function setValue($value)
    {
        if (!is_string($value)) {
            throw new InvalidValueException('Value not a string');
        }
        $this->value = $value;
    }
}
