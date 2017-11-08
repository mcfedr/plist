<?php

namespace Mcfedr\Plist\Type;

class PBooleanTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        new PBoolean();
    }

    public function testConstructorValue()
    {
        new PBoolean(true);
    }

    /**
     * @expectedException \Mcfedr\Plist\Exception\InvalidValueException
     */
    public function testConstructorInvalidValue()
    {
        new PBoolean(1);
    }
}
