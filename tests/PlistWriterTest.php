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
use Mcfedr\Plist\Type\PType;

class PlistWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testWriteArray()
    {
        $plist = new Plist(new PArray([
            new PString('first'),
            new PString('second'),
        ]));

        $writer = new PlistWriter();
        $message = $writer->write($plist);

        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist
PUBLIC "-//Apple//DTD PLIST 1.0//EN"
       "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
    <array>
        <string>first</string>
        <string>second</string>
    </array>
</plist>

XML;

        $this->assertEquals($expected, $message);
    }

    public function testWriteBoolean()
    {
        $plist = new Plist(new PDictionary([
            'true' => new PBoolean(true),
            'false' => new PBoolean(false),
        ]));

        $writer = new PlistWriter();
        $message = $writer->write($plist);

        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist
PUBLIC "-//Apple//DTD PLIST 1.0//EN"
       "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
    <dict>
        <key>true</key>
        <true/>
        <key>false</key>
        <false/>
    </dict>
</plist>

XML;

        $this->assertEquals($expected, $message);
    }

    public function testWriteData()
    {
        $plist = new Plist(new PDictionary([
            'data' => new PData('hello'),
        ]));

        $writer = new PlistWriter();
        $message = $writer->write($plist);

        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist
PUBLIC "-//Apple//DTD PLIST 1.0//EN"
       "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
    <dict>
        <key>data</key>
        <data>aGVsbG8=</data>
    </dict>
</plist>

XML;

        $this->assertEquals($expected, $message);
    }

    public function testWriteDate()
    {
        $plist = new Plist(new PDictionary([
            'date' => new PDate(\DateTime::createFromFormat('Y-m-d\TH:i:s\Z', '2017-11-08T07:28:23Z')),
        ]));

        $writer = new PlistWriter();
        $message = $writer->write($plist);

        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist
PUBLIC "-//Apple//DTD PLIST 1.0//EN"
       "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
    <dict>
        <key>date</key>
        <date>2017-11-08T07:28:23Z</date>
    </dict>
</plist>

XML;

        $this->assertEquals($expected, $message);
    }

    public function testWriteDictionary()
    {
        $plist = new Plist(new PDictionary([
            'first' => new PString('first'),
            'second' => new PString('second'),
        ]));

        $writer = new PlistWriter();
        $message = $writer->write($plist);

        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist
PUBLIC "-//Apple//DTD PLIST 1.0//EN"
       "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
    <dict>
        <key>first</key>
        <string>first</string>
        <key>second</key>
        <string>second</string>
    </dict>
</plist>

XML;

        $this->assertEquals($expected, $message);
    }

    public function testWriteInteger()
    {
        $plist = new Plist(new PDictionary([
            'number' => new PInteger(1),
        ]));

        $writer = new PlistWriter();
        $message = $writer->write($plist);

        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist
PUBLIC "-//Apple//DTD PLIST 1.0//EN"
       "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
    <dict>
        <key>number</key>
        <integer>1</integer>
    </dict>
</plist>

XML;

        $this->assertEquals($expected, $message);
    }

    public function testWriteReal()
    {
        $plist = new Plist(new PDictionary([
            'number' => new PReal(0.1),
        ]));

        $writer = new PlistWriter();
        $message = $writer->write($plist);

        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist
PUBLIC "-//Apple//DTD PLIST 1.0//EN"
       "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
    <dict>
        <key>number</key>
        <real>0.1</real>
    </dict>
</plist>

XML;

        $this->assertEquals($expected, $message);
    }

    public function testWriteString()
    {
        $plist = new Plist(new PDictionary([
            'string' => new PString('string'),
        ]));

        $writer = new PlistWriter();
        $message = $writer->write($plist);

        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist
PUBLIC "-//Apple//DTD PLIST 1.0//EN"
       "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
    <dict>
        <key>string</key>
        <string>string</string>
    </dict>
</plist>

XML;

        $this->assertEquals($expected, $message);
    }

    /**
     * @expectedException \Mcfedr\Plist\Exception\UnknownTypeException
     */
    public function testWriteUnknown()
    {
        $newElement = $this->getMockBuilder(PType::class)->getMock();

        $writer = new PlistWriter();
        $writer->write(new Plist($newElement));
    }
}
