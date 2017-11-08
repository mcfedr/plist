<?php

namespace Mcfedr\Plist\Type;

class PDataTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        new PData();
    }

    public function testConstructorValue()
    {
        new PData('string');
    }

    /**
     * @expectedException \Mcfedr\Plist\Exception\InvalidValueException
     */
    public function testConstructorInvalidValue()
    {
        new PData(1);
    }
}
