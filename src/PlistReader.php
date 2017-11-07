<?php

namespace Mcfedr\Plist;

use Mcfedr\Plist\Exception\InvalidDateStringException;
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
     * @return PType
     */
    public function read($xml, $sanitize = true)
    {
        if ($sanitize) {
            $xml = $this->sanitizeForXml($xml);
        }

        $this->reader = new \XMLReader();

        $options = LIBXML_NONET | LIBXML_COMPACT | LIBXML_HTML_NOIMPLIED | LIBXML_NOENT | LIBXML_PARSEHUGE | LIBXML_HTML_NODEFDTD;
        if (defined('LIBXML_BIGLINES')) { //From php 7.0
            $options |= LIBXML_BIGLINES;
        }
        $this->reader->XML($xml, 'UTF-8', $options);
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
                    throw new MissingKeyException('Missing key for node in dictionary');
                }

                $parent[$this->key] = $node;
                $this->key = null;
            } elseif ($parent instanceof Plist) {
                $parent->setValue($node);
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
     * Should correctly support multibyte strings, in theory
     *
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
        // Convert input to UTF-8.
        $oldSetting = mb_substitute_character();
        mb_substitute_character('none');
        $input = mb_convert_encoding($input, 'UTF-8', 'auto');
        mb_substitute_character($oldSetting);

        // Use fast preg_replace. If failure, use slower chr => int => chr conversion.
        $output = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $input);
        if (is_null($output)) {
            // Convert to ints.
            // Convert ints back into a string.
            $output = static::ordsToUtf8(static::utf8ToOrds($input), true);
        }

        return $output;
    }

    /**
     * Given a UTF-8 string, output an array of ordinal values.
     *
     * @param string $input    UTF-8 string
     * @param string $encoding Defaults to UTF-8
     *
     * @return array Array of ordinal values representing the input string
     */
    private static function utf8ToOrds($input, $encoding = 'UTF-8')
    {
        // Turn a string of unicode characters into UCS-4BE, which is a Unicode
        // encoding that stores each character as a 4 byte integer. This accounts for
        // the "UCS-4"; the "BE" prefix indicates that the integers are stored in
        // big-endian order. The reason for this encoding is that each character is a
        // fixed size, making iterating over the string simpler.
        $input = mb_convert_encoding($input, 'UCS-4BE', $encoding);

        // Visit each unicode character.
        $ords = [];
        for ($i = 0; $i < mb_strlen($input, 'UCS-4BE'); ++$i) {
            // Now we have 4 bytes. Find their total numeric value.
            $s2 = mb_substr($input, $i, 1, 'UCS-4BE');
            $val = unpack('N', $s2);
            $ords[] = $val[1];
        }

        return $ords;
    }

    /**
     * Given an array of ints representing Unicode chars, outputs a UTF-8 string.
     *
     * @param array $ords           Array of integers representing Unicode characters
     * @param bool  $sanitizeForXml Set to true to remove non valid XML characters
     *
     * @return string UTF-8 String
     */
    private static function ordsToUtf8($ords, $sanitizeForXml = false)
    {
        $output = '';
        foreach ($ords as $ord) {
            // 0: Negative numbers.
            // 55296 - 57343: Surrogate Range.
            // 65279: BOM (byte order mark).
            // 1114111: Out of range.
            if ($ord < 0
                || ($ord >= 0xD800 && $ord <= 0xDFFF)
                || $ord == 0xFEFF
                || $ord > 0x10ffff) {
                // Skip non valid UTF-8 values.
                continue;
            }
            // 9: Anything Below 9.
            // 11: Vertical Tab.
            // 12: Form Feed.
            // 14-31: Unprintable control codes.
            // 65534, 65535: Unicode noncharacters.
            elseif ($sanitizeForXml && (
                    $ord < 0x9
                    || $ord == 0xB
                    || $ord == 0xC
                    || ($ord > 0xD && $ord < 0x20)
                    || $ord == 0xFFFE
                    || $ord == 0xFFFF
                )) {
                // Skip non valid XML values.
                continue;
            }
            // 127: 1 Byte char.
            elseif ($ord <= 0x007f) {
                $output .= chr($ord);
                continue;
            }
            // 2047: 2 Byte char.
            elseif ($ord <= 0x07ff) {
                $output .= chr(0xc0 | ($ord >> 6));
                $output .= chr(0x80 | ($ord & 0x003f));
                continue;
            }
            // 65535: 3 Byte char.
            elseif ($ord <= 0xffff) {
                $output .= chr(0xe0 | ($ord >> 12));
                $output .= chr(0x80 | (($ord >> 6) & 0x003f));
                $output .= chr(0x80 | ($ord & 0x003f));
                continue;
            }
            // 1114111: 4 Byte char.
            elseif ($ord <= 0x10ffff) {
                $output .= chr(0xf0 | ($ord >> 18));
                $output .= chr(0x80 | (($ord >> 12) & 0x3f));
                $output .= chr(0x80 | (($ord >> 6) & 0x3f));
                $output .= chr(0x80 | ($ord & 0x3f));
                continue;
            }
        }

        return $output;
    }
}
