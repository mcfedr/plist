<?php

namespace Mcfedr\Plist\Type;

use Mcfedr\Plist\Exception\InvalidValueException;

class PInteger implements PType
{
    /**
     * @var int|null
     */
    private $value;

    /**
     * @param int $value
     */
    public function __construct($value = null)
    {
        if (!is_null($value)) {
            $this->setValue($value);
        }
    }

    /**
     * @return int|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param int|null $value
     */
    public function setValue($value)
    {
        if (!is_int($value)) {
            throw new InvalidValueException('Value not an int');
        }
        $this->value = $value;
    }
}
