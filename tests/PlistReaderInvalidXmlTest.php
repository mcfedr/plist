<?php

namespace Mcfedr\Plist;

use Mcfedr\Plist\Exception\XmlErrorException;
use PHPUnit\Framework\TestCase;

class PlistReaderInvalidXmlTest extends TestCase
{
    public function testInvalidXmlStructure()
    {
        $this->expectException(XmlErrorException::class);

        $message = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<dict></dict>
<dict></dict>
XML;

        $reader = new PlistReader();
        $reader->read($message);
    }
}
