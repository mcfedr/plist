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
class ReaderBench
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
     * @Groups({"read"})
     * @Revs(10)
     * @Iterations(10)
     * @OutputTimeUnit("seconds")
     */
    public function benchPlistReader()
    {
        $reader = new PlistReader();
        $reader->read($this->text);
    }

    /**
     * @Groups({"read"})
     * @Revs(10)
     * @Iterations(10)
     * @OutputTimeUnit("seconds")
     */
    public function benchCFPropertyList()
    {
        $plist = new CFPropertyList();
        $plist->parse(PlistReader::sanitizeForXml($this->text));
    }
}
