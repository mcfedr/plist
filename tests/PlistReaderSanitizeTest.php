<?php

namespace Mcfedr\Plist;

class PlistReaderSanitizeTest extends \PHPUnit_Framework_TestCase
{
    public function testSanitizeExample()
    {
        $this->assertEquals('Coco Pony', PlistReader::sanitizeForXml('Coco Pony'));
    }

    public function testSanitizeControlChars()
    {
        $this->assertEquals("\t\n\r", PlistReader::sanitizeForXml("\t\n\r"));
    }

    public function testSanitizeNonLatin()
    {
        $this->assertEquals('фисв', PlistReader::sanitizeForXml('фисв'));
    }

    public function testSanitizeInvalidControlChars()
    {
        $chars = json_decode('"\u0001\u0002\u0003"');
        $this->assertEquals('', PlistReader::sanitizeForXml($chars));
    }

    public function testSanitizeInvalidChars()
    {
        $chars = json_decode('"\ud800"');
        $this->assertEquals('', PlistReader::sanitizeForXml($chars));
    }
}
