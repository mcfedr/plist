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
	<false/>
	<integer>0</integer>
</array>
</plist>

XML;

        $reader = new PlistReader();
        $plist = $reader->read($message);

        /** @var PArray $arr */
        $arr = $plist->getValue();
        $this->assertInstanceOf(PArray::class, $arr);
        $this->assertCount(4, $arr);

        /** @var PString $first */
        $first = $arr[0];
        $this->assertInstanceOf(PString::class, $first);
        $this->assertEquals('first', $first->getValue());

        /** @var PString $second */
        $second = $arr[1];
        $this->assertInstanceOf(PString::class, $second);
        $this->assertEquals('second', $second->getValue());

        /** @var PString $third */
        $third = $arr[2];
        $this->assertInstanceOf(PBoolean::class, $third);
        $this->assertEquals(false, $third->getValue());

        /** @var PInteger $forth */
        $forth = $arr[3];
        $this->assertInstanceOf(PInteger::class, $forth);
        $this->assertEquals(0, $forth->getValue());
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

        /** @var PDictionary $dict */
        $dict = $plist->getValue();

        /** @var PBoolean $bool */
        $bool = $dict['bool'];
        $this->assertInstanceOf(PBoolean::class, $bool);
        $this->assertTrue($bool->getValue());

        /** @var PBoolean $another */
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

        /** @var PDictionary $dict */
        $dict = $plist->getValue();

        /** @var PData $data */
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

        /** @var PDictionary $dict */
        $dict = $plist->getValue();
        $this->assertInstanceOf(PDictionary::class, $dict);
        $this->assertCount(1, $dict);
        $this->assertArrayHasKey('date', $dict);

        /** @var PDate $date */
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

        /** @var PDictionary $dict */
        $dict = $plist->getValue();
        $this->assertInstanceOf(PDictionary::class, $dict);
        $this->assertCount(2, $dict);
        $this->assertArrayHasKey('first', $dict);
        $this->assertArrayHasKey('second', $dict);

        /** @var PString $first */
        $first = $dict['first'];
        $this->assertInstanceOf(PString::class, $first);
        $this->assertEquals('first', $first->getValue());

        /** @var PString $second */
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

        /** @var PDictionary $dict */
        $dict = $plist->getValue();

        /** @var PInteger $number */
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

        /** @var PDictionary $dict */
        $dict = $plist->getValue();

        /** @var PReal $number */
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

        /** @var PDictionary $dict */
        $dict = $plist->getValue();

        /** @var PString $string */
        $string = $dict['string'];
        $this->assertInstanceOf(PString::class, $string);
        $this->assertEquals('hello', $string->getValue());
    }

    public function testReadStringCdata()
    {
        $message = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
	<key>string</key>
	<string><![CDATA[hello]]></string>
</dict>
</plist>

XML;

        $reader = new PlistReader();
        $plist = $reader->read($message);

        /** @var PDictionary $dict */
        $dict = $plist->getValue();

        /** @var PString $string */
        $string = $dict['string'];
        $this->assertInstanceOf(PString::class, $string);
        $this->assertEquals('hello', $string->getValue());
    }
}
