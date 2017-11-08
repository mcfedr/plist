<?php

namespace Mcfedr\Plist\Type;

class PDictionaryTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        new PDictionary();
    }

    public function testConstructorValues()
    {
        new PDictionary([
            'key' => new PString(),
        ]);
    }

    /**
     * @expectedException \Mcfedr\Plist\Exception\InvalidValueException
     */
    public function testConstructorInvalidValues()
    {
        new PDictionary([
            'key' => 'string',
        ]);
    }

    /**
     * @expectedException \Mcfedr\Plist\Exception\InvalidKeyException
     */
    public function testConstructorInvalidKey()
    {
        new PDictionary([
            new PString(),
        ]);
    }

    /**
     * @expectedException \Mcfedr\Plist\Exception\InvalidKeyException
     */
    public function testPush()
    {
        $a = new PDictionary();
        $s = new PString();

        $a[] = $s;
    }

    public function testInsert()
    {
        $a = new PDictionary();
        $s = new PString();

        $a['key'] = $s;

        $this->assertCount(1, $a);
        $this->assertArrayHasKey('key', $a);
        $this->assertEquals($s, $a['key']);

        unset($a['key']);

        $this->assertCount(0, $a);
        $this->assertArrayNotHasKey('key', $a);
    }

    public function testForeach()
    {
        $a = new PDictionary([
            'first' => new PString(),
            'second' => new PString(),
        ]);

        foreach ($a as $key => $value) {
            $this->assertInternalType('string', $key);
            $this->assertInstanceOf(PString::class, $value);
        }
    }

    /**
     * @expectedException \Mcfedr\Plist\Exception\InvalidValueException
     */
    public function testInsertInvalidValue()
    {
        $a = new PDictionary();

        $a['key'] = 'string';
    }

    /**
     * @expectedException \Mcfedr\Plist\Exception\InvalidKeyException
     */
    public function testInsertInvalidKey()
    {
        $a = new PDictionary();

        $a[0] = new PString();
    }
}
