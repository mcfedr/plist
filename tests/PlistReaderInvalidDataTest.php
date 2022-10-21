<?php

namespace Mcfedr\Plist;

use Mcfedr\Plist\Exception\InvalidDateStringException;
use Mcfedr\Plist\Exception\InvalidStructureException;
use Mcfedr\Plist\Exception\MissingKeyException;
use Mcfedr\Plist\Exception\UnknownElementException;
use PHPUnit\Framework\TestCase;

class PlistReaderInvalidDataTest extends TestCase
{
    public function testInvalidStructure()
    {
        $this->expectException(InvalidStructureException::class);

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

    public function testInvalidRoot()
    {
        $this->expectException(InvalidStructureException::class);

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

    public function testInvalidStructureOnlyKey()
    {
        $this->expectException(InvalidStructureException::class);

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

    public function testInvalidElement()
    {
        $this->expectException(UnknownElementException::class);

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

    public function testMissingKey()
    {
        $this->expectException(MissingKeyException::class);

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

    public function testInvalidDate()
    {
        $this->expectException(InvalidDateStringException::class);

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

    public function testIncorrectNesting()
    {
        $this->expectException(InvalidStructureException::class);

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
