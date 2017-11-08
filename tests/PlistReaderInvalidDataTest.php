<?php

namespace Mcfedr\Plist;

class PlistReaderInvalidDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Mcfedr\Plist\Exception\InvalidStructureException
     */
    public function testInvalidStructure()
    {
        $message = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict></dict>
<dict></dict>
</plist>

XML;

        $reader = new PlistReader();
        $reader->read($message);
    }

    /**
     * @expectedException \Mcfedr\Plist\Exception\InvalidStructureException
     */
    public function testInvalidRoot()
    {
        $message = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<string>Idle</string>
</plist>

XML;

        $reader = new PlistReader();
        $reader->read($message);
    }

    /**
     * @expectedException \Mcfedr\Plist\Exception\InvalidStructureException
     */
    public function testInvalidStructureOnlyKey()
    {
        $message = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<key>text</key>
</plist>

XML;

        $reader = new PlistReader();
        $reader->read($message);
    }

    /**
     * @expectedException \Mcfedr\Plist\Exception\UnknownElementException
     */
    public function testInvalidElement()
    {
        $message = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<unknonwn>text</unknonwn>
</plist>

XML;

        $reader = new PlistReader();
        $reader->read($message);
    }

    /**
     * @expectedException \Mcfedr\Plist\Exception\MissingKeyException
     */
    public function testMissingKey()
    {
        $message = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
<string>string</string>
</dict>
</plist>

XML;

        $reader = new PlistReader();
        $reader->read($message);
    }

    /**
     * @expectedException \Mcfedr\Plist\Exception\InvalidDateStringException
     */
    public function testInvalidDate()
    {
        $message = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
<key>date</key>
<date>08/11/2017</date>
</dict>
</plist>

XML;

        $reader = new PlistReader();
        $reader->read($message);
    }

    /**
     * @expectedException \Mcfedr\Plist\Exception\InvalidStructureException
     */
    public function testIncorrectNesting()
    {
        $message = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
<key>key</key>
<string>
<string>string</string>
</string>
</dict>
</plist>

XML;

        $reader = new PlistReader();
        $reader->read($message);
    }
}
