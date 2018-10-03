<?php

namespace Mcfedr\Plist;

use Mcfedr\Plist\Exception\InvalidStructureException;
use Mcfedr\Plist\Exception\UnknownTypeException;
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

class PlistWriter
{
    /**
     * @var \XMLWriter
     */
    private $writer;

    public function write(Plist $plist)
    {
        $this->writer = new \XMLWriter();
        $this->writer->openMemory();
        $this->writer->setIndent(true);
        $this->writer->setIndentString('    ');

        $this->writer->startDocument('1.0', 'UTF-8');

        $this->writer->startDTD('plist', '-//Apple//DTD PLIST 1.0//EN', 'http://www.apple.com/DTDs/PropertyList-1.0.dtd');
        $this->writer->endDTD();

        $this->writer->startElement('plist');
        $this->writer->startAttribute('version');
        $this->writer->text('1.0');
        $this->writer->endAttribute();

        if (!$plist->getValue()) {
            throw new InvalidStructureException('Expected a value as the root element but got null');
        }

        $this->writePType($plist->getValue());

        $this->writer->endElement();

        return $this->writer->outputMemory();
    }

    private function writePType(PType $element)
    {
        if ($element instanceof PArray) {
            $this->writer->startElement('array');
            foreach ($element as $item) {
                $this->writePType($item);
            }
            $this->writer->endElement();
        } elseif ($element instanceof PBoolean) {
            if ($element->getValue()) {
                $this->writer->writeElement('true');
            } else {
                $this->writer->writeElement('false');
            }
        } elseif ($element instanceof PData) {
            $this->writer->startElement('data');
            $this->writer->text(base64_encode($element->getValue()));
            $this->writer->endElement();
        } elseif ($element instanceof PDate) {
            $this->writer->startElement('date');
            $this->writer->text($element->getValue()->format(PDate::FORMAT));
            $this->writer->endElement();
        } elseif ($element instanceof PDictionary) {
            $this->writer->startElement('dict');
            foreach ($element as $key => $item) {
                $this->writer->startElement('key');
                $this->writer->text($key);
                $this->writer->endElement();
                $this->writePType($item);
            }
            $this->writer->endElement();
        } elseif ($element instanceof PInteger) {
            $this->writer->startElement('integer');
            $this->writer->text((string) $element->getValue());
            $this->writer->endElement();
        } elseif ($element instanceof PReal) {
            $this->writer->startElement('real');
            $this->writer->text((string) $element->getValue());
            $this->writer->endElement();
        } elseif ($element instanceof PString) {
            $this->writer->startElement('string');
            $this->writer->text($element->getValue());
            $this->writer->endElement();
        } else {
            throw new UnknownTypeException('Cannot write unknown element ('.get_class($element).')');
        }
    }
}
