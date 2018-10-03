<?php

namespace Mcfedr\Plist\Type;

use Mcfedr\Plist\Exception\InvalidKeyException;
use Mcfedr\Plist\Exception\InvalidValueException;

class PArray implements PRoot, \ArrayAccess, \Countable, \Iterator
{
    /**
     * @var PType[]
     */
    private $elements;

    /**
     * @param PType[] $elements
     */
    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
        foreach ($this->elements as $key => $value) {
            if (!is_int($key)) {
                throw new InvalidKeyException('Key must be an int');
            }
            if (!$value instanceof PType) {
                throw new InvalidValueException('Value not an instance of PType');
            }
        }
    }

    /**
     * @param int $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->elements[$offset]);
    }

    /**
     * @param int $offset
     * @return PType
     */
    public function offsetGet($offset)
    {
        return $this->elements[$offset];
    }

    /**
     * @param int $offset
     * @param PType $value
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof PType) {
            throw new InvalidValueException('Value not an instance of PType');
        }

        if (is_null($offset)) {
            $this->elements[] = $value;

            return;
        }

        if (!is_int($offset)) {
            throw new InvalidKeyException('Key must be an int');
        }
        $this->elements[$offset] = $value;
    }

    /**
     * @param int $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->elements[$offset]);
    }

    public function count()
    {
        return count($this->elements);
    }

    /**
     * @return PType
     */
    public function current()
    {
        return current($this->elements);
    }

    public function next()
    {
        next($this->elements);
    }

    /**
     * @return int
     */
    public function key()
    {
        return key($this->elements);
    }

    public function valid()
    {
        return isset($this->elements[key($this->elements)]);
    }

    public function rewind()
    {
        reset($this->elements);
    }
}
