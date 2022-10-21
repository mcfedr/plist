<?php

namespace Mcfedr\Plist\Type;

use Mcfedr\Plist\Exception\InvalidKeyException;
use Mcfedr\Plist\Exception\InvalidValueException;
use PHPUnit\Framework\TestCase;

class PArrayTest extends TestCase
{
    public function testConstructor()
    {
        $this->expectNotToPerformAssertions();

        new PArray();
    }

    public function testConstructorValues()
    {
        $this->expectNotToPerformAssertions();

        new PArray([
            new PString(),
        ]);
    }

    public function testConstructorInvalidValues()
    {
        $this->expectException(InvalidValueException::class);

        new PArray([
            'string',
        ]);
    }

    public function testConstructorInvalidKey()
    {
        $this->expectException(InvalidKeyException::class);

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
            $this->assertIsInt($key);
            $this->assertInstanceOf(PString::class, $value);
        }
    }

    public function testInsertInvalidValue()
    {
        $this->expectException(InvalidValueException::class);

        $a = new PArray();

        $a[0] = 'string';
    }

    public function testInsertInvalidKey()
    {
        $this->expectException(InvalidKeyException::class);

        $a = new PArray();

        $a['key'] = new PString();
    }
}
