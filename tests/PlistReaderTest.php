<?php

namespace Mcfedr\Plist;

use Mcfedr\Plist\Type\PArray;
use Mcfedr\Plist\Type\PDictionary;
use Mcfedr\Plist\Type\PInteger;
use Mcfedr\Plist\Type\Plist;
use Mcfedr\Plist\Type\PString;

class PlistReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testRead()
    {
        $message = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>Status</key>
    <string>Idle</string>
    <key>UDID</key>
    <string>abcd</string>
</dict>
</plist>

XML;

        $reader = new PlistReader();
        $plist = $reader->read($message);

        $this->assertInstanceOf(Plist::class, $plist);

        $dict = $plist->getValue();
        $this->assertInstanceOf(PDictionary::class, $dict);
        $this->assertCount(2, $dict);
        $this->assertArrayHasKey('Status', $dict);
        $this->assertArrayHasKey('UDID', $dict);

        $status = $dict['Status'];
        $this->assertInstanceOf(PString::class, $status);
        $this->assertEquals('Idle', $status->getValue());

        $udid = $dict['UDID'];
        $this->assertInstanceOf(PString::class, $udid);
        $this->assertEquals('abcd', $udid->getValue());
    }

    public function testReadNested()
    {
        $message = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
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

        $reader = new PlistReader();
        $plist = $reader->read($message);

        $this->assertInstanceOf(Plist::class, $plist);

        $dict = $plist->getValue();
        $this->assertInstanceOf(PDictionary::class, $dict);
        $this->assertCount(2, $dict);
        $this->assertArrayHasKey('Command', $dict);
        $this->assertArrayHasKey('CommandUUID', $dict);

        $command = $dict['Command'];
        $this->assertInstanceOf(PDictionary::class, $command);
        $this->assertCount(2, $command);
        $this->assertArrayHasKey('RequestType', $command);
        $this->assertArrayHasKey('Identifier', $command);

        $commandUuid = $dict['CommandUUID'];
        $this->assertInstanceOf(PString::class, $commandUuid);
        $this->assertEquals('abcd', $commandUuid->getValue());
    }

    public function testReadInvalidXml()
    {
        $message = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
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

        $reader = new PlistReader();
        $plist = $reader->read($message);

        $this->assertInstanceOf(Plist::class, $plist);

        $dict = $plist->getValue();
        $this->assertInstanceOf(PDictionary::class, $dict);
        $this->assertCount(4, $dict);
        $this->assertArrayHasKey('命令UUID', $dict);
        $this->assertArrayHasKey('InstalledApplicationList', $dict);
        $this->assertArrayHasKey('Статус', $dict);
        $this->assertArrayHasKey('UDID', $dict);

        $list = $dict['InstalledApplicationList'];
        $this->assertInstanceOf(PArray::class, $list);
        $this->assertCount(1, $list);

        $app = $list[0];
        $this->assertInstanceOf(PDictionary::class, $app);
        $this->assertCount(6, $app);
        $this->assertArrayHasKey('BundleSize', $app);
        $this->assertArrayHasKey('DynamicSize', $app);
        $this->assertArrayHasKey('Identifier', $app);
        $this->assertArrayHasKey('Name', $app);
        $this->assertArrayHasKey('ShortVersion', $app);
        $this->assertArrayHasKey('Version', $app);

        $size = $app['BundleSize'];
        $this->assertInstanceOf(PInteger::class, $size);

        $udid = $dict['UDID'];
        $this->assertInstanceOf(PString::class, $udid);
        $this->assertEquals('abcd', $udid->getValue());

        $status = $dict['Статус'];
        $this->assertInstanceOf(PString::class, $status);
        $this->assertEquals('承認された', $status->getValue());

        $commandUuid = $dict['命令UUID'];
        $this->assertInstanceOf(PString::class, $commandUuid);
        $this->assertEquals('abcd', $commandUuid->getValue());
    }

    /**
     * @dataProvider requests
     */
    public function testRequest($request)
    {
        $request = file_get_contents($request);

        $reader = new PlistReader();
        $plist = $reader->read($request);

        $this->assertInstanceOf(Plist::class, $plist);
    }

    public function requests()
    {
        $files = scandir(__DIR__.'/fixtures');
        $files = array_filter($files, function ($file) {
            return $file !== '.' && $file !== '..';
        });

        return array_map(function ($file) {
            return [
                __DIR__."/fixtures/$file",
            ];
        }, $files);
    }
}
