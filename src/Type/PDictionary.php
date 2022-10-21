<?php

namespace Mcfedr\Plist\Type;

use Mcfedr\Plist\Exception\InvalidKeyException;
use Mcfedr\Plist\Exception\InvalidValueException;

class PDictionary implements PRoot, \ArrayAccess, \Countable, \Iterator
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
            if (!is_string($key)) {
                throw new InvalidKeyException('Key must be a string');
            }
            if (!$value instanceof PType) {
                throw new InvalidValueException('Value not an instance of PType');
            }
        }
    }

    public function offsetExists($offset): bool
    {
        return isset($this->elements[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->elements[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        if (!is_string($offset)) {
            throw new InvalidKeyException('Key must be a string');
        }
        if (!$value instanceof PType) {
            throw new InvalidValueException('Value not an instance of PType');
        }
        $this->elements[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->elements[$offset]);
    }

    public function count(): int
    {
        return count($this->elements);
    }

    public function current(): mixed
    {
        return current($this->elements);
    }

    public function next(): void
    {
        next($this->elements);
    }

    public function key(): mixed
    {
        return key($this->elements);
    }

    public function valid(): bool
    {
        return isset($this->elements[key($this->elements)]);
    }

    public function rewind(): void
    {
        reset($this->elements);
    }
}
