<?php

namespace Mcfedr\Plist;

use Mcfedr\Plist\Exception\InvalidDateStringException;
use Mcfedr\Plist\Exception\InvalidStructureException;
use Mcfedr\Plist\Exception\MissingKeyException;
use Mcfedr\Plist\Exception\UnknownElementException;
use Mcfedr\Plist\Type\PArray;
use Mcfedr\Plist\Type\PBoolean;
use Mcfedr\Plist\Type\PData;
use Mcfedr\Plist\Type\PDate;
use Mcfedr\Plist\Type\PDictionary;
use Mcfedr\Plist\Type\PInteger;
use Mcfedr\Plist\Type\Plist;
use Mcfedr\Plist\Type\PReal;
use Mcfedr\Plist\Type\PRoot;
use Mcfedr\Plist\Type\PString;
use Mcfedr\Plist\Type\PType;

class PlistReader
{
    const STATE_START = 0;
    const STATE_KEY = 1;
    const STATE_NODE = 2;

    /**
     * @var \XMLReader
     */
    private $reader;

    /**
     * @var int
     */
    private $state;

    /**
     * @var PType[]
     */
    private $nodes;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $text;

    /**
     * @param string $xml
     * @param bool   $sanitize Clean up invalid characters in the xml
     *
     * @return Plist
     */
    public function read($xml, $sanitize = true)
    {
        if ($sanitize) {
            $xml = $this->sanitizeForXml($xml);
        }

        $this->reader = new \XMLReader();

        $this->reader->XML($xml, 'UTF-8', LIBXML_NONET | LIBXML_COMPACT | LIBXML_HTML_NOIMPLIED | LIBXML_NOENT | LIBXML_PARSEHUGE | LIBXML_HTML_NODEFDTD);
        $this->reader->setParserProperty(\XMLReader::SUBST_ENTITIES, true);

        $this->state = self::STATE_START;
        $this->nodes = [];

        $last = null;

        while ($this->reader->read()) {
            switch ($this->reader->nodeType) {
                case \XMLReader::ELEMENT:
                    $last = $this->readElement();
                    break;
                case \XMLReader::TEXT:
                    $this->readText();
                    break;
                case \XMLReader::END_ELEMENT:
                    $last = $this->readEndElement();
                    break;
            }
        }

        $this->reader->close();

        if (!$last instanceof Plist || !$last->getValue()) {
            throw new InvalidStructureException('Expected a plist as the root element but got '.get_class($last));
        }

        return $last;
    }

    private function readElement()
    {
        $elementName = $this->reader->name;
        if ($elementName == 'plist') {
            $this->nodes[] = new Plist();
        } elseif ($elementName == 'dict') {
            $this->nodes[] = new PDictionary();
        } elseif ($elementName == 'key') {
            $this->text = '';
            $this->state = self::STATE_KEY;

            return;
        } elseif ($elementName == 'array') {
            $this->nodes[] = new PArray();
        } elseif ($elementName == 'string') {
            $this->nodes[] = new PString();
            $this->text = '';
            $this->state = self::STATE_NODE;
        } elseif ($elementName == 'true') {
            $this->nodes[] = new PBoolean(true);
        } elseif ($elementName == 'false') {
            $this->nodes[] = new PBoolean(false);
        } elseif ($elementName == 'real') {
            $this->nodes[] = new PReal();
            $this->text = '';
            $this->state = self::STATE_NODE;
        } elseif ($elementName == 'integer') {
            $this->nodes[] = new PInteger();
            $this->text = '';
            $this->state = self::STATE_NODE;
        } elseif ($elementName == 'data') {
            $this->nodes[] = new PData();
            $this->text = '';
            $this->state = self::STATE_NODE;
        } elseif ($elementName == 'date') {
            $this->nodes[] = new PDate();
            $this->text = '';
            $this->state = self::STATE_NODE;
        } else {
            throw new UnknownElementException("Trying to parse unknown element ($elementName)");
        }

        if (($count = count($this->nodes)) > 1) {
            $node = $this->nodes[$count - 1];
            $parent = $this->nodes[$count - 2];

            if ($parent instanceof PArray) {
                $parent[] = $node;
            } elseif ($parent instanceof PDictionary) {
                if (!$this->key) {
                    throw new MissingKeyException('Missing key for node ('.get_class($node).') in dictionary');
                }

                $parent[$this->key] = $node;
                $this->key = null;
            } elseif ($parent instanceof Plist) {
                if (!$node instanceof PRoot) {
                    throw new InvalidStructureException('Trying to insert non root node into plist ('.get_class($node).')');
                }
                if ($parent->getValue()) {
                    throw new InvalidStructureException('Trying to insert multiple values into plist');
                }

                $parent->setValue($node);
            } else {
                throw new InvalidStructureException('Trying to insert a node into a non containing parent ('.get_class($parent).')');
            }
        }

        if ($this->reader->isEmptyElement) {
            return array_pop($this->nodes);
        }

        return null;
    }

    private function readText()
    {
        if ($this->state === self::STATE_KEY || $this->state === self::STATE_NODE) {
            $this->text .= $this->reader->value;
        }
    }

    private function readEndElement()
    {
        if ($this->state === self::STATE_KEY) {
            $this->key = $this->text;

            $this->state = self::STATE_START;
            $this->text = null;

            return null;
        }

        $node = array_pop($this->nodes);

        if ($this->state == self::STATE_NODE) {
            if ($node instanceof PString) {
                $node->setValue($this->text);
            } elseif ($node instanceof PReal) {
                $node->setValue((float) $this->text);
            } elseif ($node instanceof PInteger) {
                $node->setValue((int) $this->text);
            } elseif ($node instanceof PData) {
                $node->setValue(base64_decode($this->text));
            } elseif ($node instanceof PDate) {
                if (($date = \DateTime::createFromFormat(PDate::FORMAT, $this->text)) === false) {
                    throw new InvalidDateStringException("Invalid date string ({$this->text})");
                }
                $node->setValue($date);
            }

            $this->state = self::STATE_START;
            $this->text = null;
        }

        return $node;
    }

    /**
     * Removes invalid XML characters.
     *
     * @see https://en.wikipedia.org/wiki/Valid_characters_in_XML
     *
     * Should correctly support multibyte strings, in theory
     * @see https://stackoverflow.com/a/22852943/859027
     *
     * Not using https://stackoverflow.com/a/3466049/859027
     * as it doesnt support utf8 correctly
     *
     * @param string $input
     *
     * @return string
     */
    public static function sanitizeForXml($input)
    {
        // Remove invalid utf8 characters
        $oldSetting = mb_substitute_character();
        mb_substitute_character('none');
        $input = mb_convert_encoding($input, 'UTF-8', 'auto');
        mb_substitute_character($oldSetting);

        // Remove invalid xml characters
        return preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $input);
    }
}
