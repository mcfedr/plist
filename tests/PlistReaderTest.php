<?php

namespace Mcfedr\Plist;

use Mcfedr\Plist\Type\PArray;
use Mcfedr\Plist\Type\PBoolean;
use Mcfedr\Plist\Type\PData;
use Mcfedr\Plist\Type\PDate;
use Mcfedr\Plist\Type\PDictionary;
use Mcfedr\Plist\Type\PInteger;
use Mcfedr\Plist\Type\Plist;
use Mcfedr\Plist\Type\PReal;
use Mcfedr\Plist\Type\PString;

class PlistReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testReadArray()
    {
        $message = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<array>
	<string>first</string>
	<string>second</string>
</array>
</plist>

XML;

        $reader = new PlistReader();
        $plist = $reader->read($message);

        $arr = $plist->getValue();
        $this->assertInstanceOf(PArray::class, $arr);
        $this->assertCount(2, $arr);

        $first = $arr[0];
        $this->assertInstanceOf(PString::class, $first);
        $this->assertEquals('first', $first->getValue());

        $second = $arr[1];
        $this->assertInstanceOf(PString::class, $second);
        $this->assertEquals('second', $second->getValue());
    }

    public function testReadBoolean()
    {
        $message = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>bool</key>
    <true />
    <key>another</key>
    <false/>
</dict>
</plist>

XML;

        $reader = new PlistReader();
        $plist = $reader->read($message);

        $dict = $plist->getValue();
        $bool = $dict['bool'];
        $this->assertInstanceOf(PBoolean::class, $bool);
        $this->assertTrue($bool->getValue());

        $another = $dict['another'];
        $this->assertInstanceOf(PBoolean::class, $another);
        $this->assertFalse($another->getValue());
    }

    public function testReadData()
    {
        $message = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>data</key>
    <data>aGVsbG8=</data>
</dict>
</plist>

XML;

        $reader = new PlistReader();
        $plist = $reader->read($message);

        $dict = $plist->getValue();
        $data = $dict['data'];
        $this->assertInstanceOf(PData::class, $data);
        $this->assertEquals('hello', $data->getValue());
    }

    public function testReadDate()
    {
        $message = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
	<key>date</key>
	<date>2017-11-08T07:28:23Z</date>
</dict>
</plist>

XML;

        $reader = new PlistReader();
        $plist = $reader->read($message);

        $dict = $plist->getValue();
        $this->assertInstanceOf(PDictionary::class, $dict);
        $this->assertCount(1, $dict);
        $this->assertArrayHasKey('date', $dict);

        $date = $dict['date'];
        $this->assertInstanceOf(PDate::class, $date);
        $this->assertEquals(\DateTime::createFromFormat('Y-m-d\TH:i:s\Z', '2017-11-08T07:28:23Z'), $date->getValue());
    }

    public function testReadDictionary()
    {
        $message = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
	<key>first</key>
	<string>first</string>
	<key>second</key>
	<string>second</string>
</dict>
</plist>


XML;

        $reader = new PlistReader();
        $plist = $reader->read($message);

        $this->assertInstanceOf(Plist::class, $plist);

        $dict = $plist->getValue();
        $this->assertInstanceOf(PDictionary::class, $dict);
        $this->assertCount(2, $dict);
        $this->assertArrayHasKey('first', $dict);
        $this->assertArrayHasKey('second', $dict);

        $first = $dict['first'];
        $this->assertInstanceOf(PString::class, $first);
        $this->assertEquals('first', $first->getValue());

        $second = $dict['second'];
        $this->assertInstanceOf(PString::class, $second);
        $this->assertEquals('second', $second->getValue());
    }

    public function testReadInteger()
    {
        $message = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
	<key>number</key>
	<integer>1</integer>
</dict>
</plist>

XML;

        $reader = new PlistReader();
        $plist = $reader->read($message);

        $dict = $plist->getValue();
        $number = $dict['number'];
        $this->assertInstanceOf(PInteger::class, $number);
        $this->assertEquals(1, $number->getValue());
    }

    public function testReadReal()
    {
        $message = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
	<key>number</key>
	<real>0.1</real>
</dict>
</plist>

XML;

        $reader = new PlistReader();
        $plist = $reader->read($message);

        $dict = $plist->getValue();
        $number = $dict['number'];
        $this->assertInstanceOf(PReal::class, $number);
        $this->assertEquals(0.1, $number->getValue());
    }

    public function testReadString()
    {
        $message = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
	<key>string</key>
	<string>hello</string>
</dict>
</plist>

XML;

        $reader = new PlistReader();
        $plist = $reader->read($message);

        $dict = $plist->getValue();
        $string = $dict['string'];
        $this->assertInstanceOf(PString::class, $string);
        $this->assertEquals('hello', $string->getValue());
    }
}
