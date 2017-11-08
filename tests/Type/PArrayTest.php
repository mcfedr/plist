<?php

namespace Mcfedr\Plist\Type;

class PArrayTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        new PArray();
    }

    public function testConstructorValues()
    {
        new PArray([
            new PString(),
        ]);
    }

    /**
     * @expectedException \Mcfedr\Plist\Exception\InvalidValueException
     */
    public function testConstructorInvalidValues()
    {
        new PArray([
            'string',
        ]);
    }

    /**
     * @expectedException \Mcfedr\Plist\Exception\InvalidKeyException
     */
    public function testConstructorInvalidKey()
    {
        new PArray([
            'key' => new PString(),
        ]);
    }

    public function testPush()
    {
        $a = new PArray();
        $s = new PString();

        $a[] = $s;

        $this->assertCount(1, $a);
        $this->assertArrayHasKey(0, $a);
        $this->assertEquals($s, $a[0]);
    }

    public function testInsert()
    {
        $a = new PArray();
        $s = new PString();

        $a[0] = $s;

        $this->assertCount(1, $a);
        $this->assertArrayHasKey(0, $a);
        $this->assertEquals($s, $a[0]);

        unset($a[0]);

        $this->assertCount(0, $a);
        $this->assertArrayNotHasKey(0, $a);
    }

    public function testForeach()
    {
        $a = new PArray([
            new PString(),
            new PString(),
        ]);

        foreach ($a as $key => $value) {
            $this->assertInternalType('int', $key);
            $this->assertInstanceOf(PString::class, $value);
        }
    }

    /**
     * @expectedException \Mcfedr\Plist\Exception\InvalidValueException
     */
    public function testInsertInvalidValue()
    {
        $a = new PArray();

        $a[0] = 'string';
    }

    /**
     * @expectedException \Mcfedr\Plist\Exception\InvalidKeyException
     */
    public function testInsertInvalidKey()
    {
        $a = new PArray();

        $a['key'] = new PString();
    }
}
