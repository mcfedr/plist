<?php

namespace Mcfedr\Plist\Type;

use Mcfedr\Plist\Exception\InvalidValueException;
use PHPUnit\Framework\TestCase;

class PDataTest extends TestCase
{
    public function testConstructor()
    {
        $this->expectNotToPerformAssertions();

        new PData();
    }

    public function testConstructorValue()
    {
        $this->expectNotToPerformAssertions();

        new PData('string');
    }

    public function testConstructorInvalidValue()
    {
        $this->expectException(InvalidValueException::class);
        new PData(1);
    }
}
