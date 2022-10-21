<?php

namespace Mcfedr\Plist\Type;

use Mcfedr\Plist\Exception\InvalidValueException;
use PHPUnit\Framework\TestCase;

class PIntegerTest extends TestCase
{
    public function testConstructor()
    {
        $this->expectNotToPerformAssertions();

        new PInteger();
    }

    public function testConstructorValue()
    {
        $this->expectNotToPerformAssertions();

        new PInteger(1);
    }

    /**
     * @expectedException \Mcfedr\Plist\Exception\InvalidValueException
     */
    public function testConstructorInvalidValue()
    {
        $this->expectException(InvalidValueException::class);

        new PInteger('string');
    }
}
