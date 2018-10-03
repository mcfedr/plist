<?php

namespace Mcfedr\Plist\Type;

use Mcfedr\Plist\Exception\InvalidValueException;

class PBoolean implements PType
{
    /**
     * @var bool|null
     */
    private $value;

    /**
     * @param bool $value
     */
    public function __construct($value = null)
    {
        if (!is_null($value)) {
            $this->setValue($value);
        }
    }

    /**
     * @return bool|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param bool|null $value
     */
    public function setValue($value)
    {
        if (!is_bool($value)) {
            throw new InvalidValueException('Value not a bool');
        }
        $this->value = $value;
    }
}
