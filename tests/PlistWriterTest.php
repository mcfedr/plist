<?php

namespace Mcfedr\Plist;

use Mcfedr\Plist\Type\PArray;
use Mcfedr\Plist\Type\PDictionary;
use Mcfedr\Plist\Type\PInteger;
use Mcfedr\Plist\Type\Plist;
use Mcfedr\Plist\Type\PString;

class PlistWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $plist = new Plist();
        $plist->setValue(new PString('Hello'));

        $writer = new PlistWriter();
        $message = $writer->write($plist);

        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist
PUBLIC "-//Apple//DTD PLIST 1.0//EN"
       "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
    <string>Hello</string>
</plist>

XML;

        $this->assertEquals($expected, $message);
    }

    public function testWriteDict()
    {
        $plist = new Plist();
        $dict = new PDictionary();
        $plist->setValue($dict);

        $dict['Status'] = new PString('Idle');
        $dict['UDID'] = new PString('abcd');

        $writer = new PlistWriter();
        $message = $writer->write($plist);

        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist
PUBLIC "-//Apple//DTD PLIST 1.0//EN"
       "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
    <dict>
        <key>Status</key>
        <string>Idle</string>
        <key>UDID</key>
        <string>abcd</string>
    </dict>
</plist>

XML;

        $this->assertEquals($expected, $message);
    }

    public function testWriteNested()
    {
        $plist = new Plist(new PDictionary([
            'Command' => new PDictionary([
                'RequestType' => new PString('RemoveProfile'),
                'Identifier' => new PString('com.kidslox.kidslox'),
            ]),
            'CommandUUID' => new PString('abcd'),
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
        <key>Command</key>
        <dict>
            <key>RequestType</key>
            <string>RemoveProfile</string>
            <key>Identifier</key>
            <string>com.kidslox.kidslox</string>
        </dict>
        <key>CommandUUID</key>
        <string>abcd</string>
    </dict>
</plist>

XML;

        $this->assertEquals($expected, $message);
    }

    public function testWriteInvalidXml()
    {
        $plist = new Plist(new PDictionary([
            '命令UUID' => new PString('abcd'),
            'InstalledApplicationList' => new PArray([
                new PDictionary([
                    'BundleSize' => new PInteger(237867008),
                    'DynamicSize' => new PInteger(108572672),
                    'Identifier' => new PString('com.cocoplay.cocopony'),
                    'Name' => new PString('Coco Pony'),
                    'ShortVersion' => new PString('0.8.2'),
                    'Version' => new PString('0.8.2'),
                ]),
            ]),
            'Статус' => new PString('承認された'),
            'UDID' => new PString('abcd'),
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
        <key>命令UUID</key>
        <string>abcd</string>
        <key>InstalledApplicationList</key>
        <array>
            <dict>
                <key>BundleSize</key>
                <integer>237867008</integer>
                <key>DynamicSize</key>
                <integer>108572672</integer>
                <key>Identifier</key>
                <string>com.cocoplay.cocopony</string>
                <key>Name</key>
                <string>Coco Pony</string>
                <key>ShortVersion</key>
                <string>0.8.2</string>
                <key>Version</key>
                <string>0.8.2</string>
            </dict>
        </array>
        <key>Статус</key>
        <string>承認された</string>
        <key>UDID</key>
        <string>abcd</string>
    </dict>
</plist>

XML;

        $this->assertEquals($expected, $message);
    }
}
