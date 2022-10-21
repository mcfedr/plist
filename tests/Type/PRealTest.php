<?php

namespace Mcfedr\Plist\Type;

use Mcfedr\Plist\Exception\InvalidValueException;
use PHPUnit\Framework\TestCase;

class PRealTest extends TestCase
{
    public function testConstructor()
    {
        $this->expectNotToPerformAssertions();
        new PReal();
    }

    public function testConstructorValue()
    {
        $this->expectNotToPerformAssertions();

        new PReal(1.0);
    }

    public function testConstructorInvalidValue()
    {
        $this->expectException(InvalidValueException::class);

        new PReal('string');
    }
}
