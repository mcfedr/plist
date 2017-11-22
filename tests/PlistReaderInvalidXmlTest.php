<?php

namespace Mcfedr\Plist;

class PlistReaderInvalidXmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Mcfedr\Plist\Exception\XmlErrorException
     */
    public function testInvalidXmlStructure()
    {
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
