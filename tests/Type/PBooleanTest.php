<?php

namespace Mcfedr\Plist\Type;

use Mcfedr\Plist\Exception\InvalidValueException;
use PHPUnit\Framework\TestCase;

class PBooleanTest extends TestCase
{
    public function testConstructor()
    {
        $this->expectNotToPerformAssertions();

        new PBoolean();
    }

    public function testConstructorValue()
    {
        $this->expectNotToPerformAssertions();

        new PBoolean(true);
    }

    public function testConstructorInvalidValue()
    {
        $this->expectException(InvalidValueException::class);

        new PBoolean(1);
    }
}
