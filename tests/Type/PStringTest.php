<?php

namespace Mcfedr\Plist\Type;

class PStringTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        new PString();
    }

    public function testConstructorValue()
    {
        new PString('string');
    }

    /**
     * @expectedException \Mcfedr\Plist\Exception\InvalidValueException
     */
    public function testConstructorInvalidValue()
    {
        new PString(1);
    }
}
