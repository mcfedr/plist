<?php

namespace Mcfedr\Plist\Type;

class PIntegerTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        new PInteger();
    }

    public function testConstructorValue()
    {
        new PInteger(1);
    }

    /**
     * @expectedException \Mcfedr\Plist\Exception\InvalidValueException
     */
    public function testConstructorInvalidValue()
    {
        new PInteger('string');
    }
}
