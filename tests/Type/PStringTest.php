<?php

namespace Mcfedr\Plist\Type;

use Mcfedr\Plist\Exception\InvalidValueException;
use PHPUnit\Framework\TestCase;

class PStringTest extends TestCase
{
    public function testConstructor()
    {
        $this->expectNotToPerformAssertions();

        new PString();
    }

    public function testConstructorValue()
    {
        $this->expectNotToPerformAssertions();

        new PString('string');
    }

    public function testConstructorInvalidValue()
    {
        $this->expectException(InvalidValueException::class);

        new PString(1);
    }
}
