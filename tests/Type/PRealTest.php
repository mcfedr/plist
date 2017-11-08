<?php

namespace Mcfedr\Plist\Type;

class PRealTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        new PReal();
    }

    public function testConstructorValue()
    {
        new PReal(1.0);
    }

    /**
     * @expectedException \Mcfedr\Plist\Exception\InvalidValueException
     */
    public function testConstructorInvalidValue()
    {
        new PReal('string');
    }
}
