<?php

namespace Mcfedr\Plist\Type;

use Mcfedr\Plist\Exception\InvalidKeyException;
use Mcfedr\Plist\Exception\InvalidValueException;
use PHPUnit\Framework\TestCase;

class PDictionaryTest extends TestCase
{
    public function testConstructor()
    {
        $this->expectNotToPerformAssertions();

        new PDictionary();
    }

    public function testConstructorValues()
    {
        $this->expectNotToPerformAssertions();

        new PDictionary([
            'key' => new PString(),
        ]);
    }

    public function testConstructorInvalidValues()
    {
        $this->expectException(InvalidValueException::class);
        new PDictionary([
            'key' => 'string',
        ]);
    }

    public function testConstructorInvalidKey()
    {
        $this->expectException(InvalidKeyException::class);

        new PDictionary([
            new PString(),
        ]);
    }

    public function testPush()
    {
        $this->expectException(InvalidKeyException::class);
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
            $this->assertIsString($key);
            $this->assertInstanceOf(PString::class, $value);
        }
    }

    public function testInsertInvalidValue()
    {
        $this->expectException(InvalidValueException::class);
        $a = new PDictionary();

        $a['key'] = 'string';
    }

    public function testInsertInvalidKey()
    {
        $this->expectException(InvalidKeyException::class);

        $a = new PDictionary();

        $a[0] = new PString();
    }
}
