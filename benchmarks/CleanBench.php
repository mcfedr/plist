<?php

namespace Mcfedr\Plist;

use CFPropertyList\CFPropertyList;
use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use PhpBench\Benchmark\Metadata\Annotations\Groups;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\OutputTimeUnit;
use PhpBench\Benchmark\Metadata\Annotations\Revs;

/**
 * @BeforeMethods({"before"})
 */
class CleanBench
{
    /**
     * @var string
     */
    private $text;

    public function before()
    {
        $this->text = file_get_contents(__DIR__ . '/fixtures/mdm_request.xml');
    }

    /**
     * @Groups({"clean"})
     * @Revs(10)
     * @Iterations(10)
     * @OutputTimeUnit("seconds")
     */
    public function benchPlistReaderCleaner()
    {
        PlistReader::sanitizeForXml($this->text);
    }

    /**
     * @Groups({"clean"})
     * @Revs(10)
     * @Iterations(10)
     * @OutputTimeUnit("seconds")
     */
    public function benchCFPropertyList()
    {
        $this->clean($this->text);
    }

    private function clean($xml) {
        // See the test testMdmActionInvalidXMLAppsResponse
        // Replaces invalid XML chars (note: its not invalid utf8 so iconv '//IGNORE' and utf8_encode do not help)
        // The simpler find replace version was not working as a whole variety of invalid chars are coming
        // http://stackoverflow.com/a/17029396/859027
        // http://stackoverflow.com/a/8092672/859027

        $prev = libxml_use_internal_errors(true);
        libxml_clear_errors(); // Make sure we do not report any old errors

        $doc = new \DOMDocument();
        $doc->recover = true;
        $doc->loadXML($xml);

        libxml_clear_errors();
        libxml_use_internal_errors($prev); //Make sure we put everything back how we found it

        $cleanXML = $doc->saveXML();

        return $cleanXML;
    }
}
