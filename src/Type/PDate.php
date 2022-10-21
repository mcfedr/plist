<?php

namespace Mcfedr\Plist\Type;

class PDate implements PType
{
    public const FORMAT = 'Y-m-d\TH:i:s\Z';

    /**
     * @var \DateTime
     */
    private $value;

    /**
     * @param \DateTime $value
     */
    public function __construct(\DateTime $value = null)
    {
        $this->value = $value;
    }

    /**
     * @return \DateTime
     */
    public function getValue()
    {
        return $this->value;
    }

    public function setValue(\DateTime $value)
    {
        $this->value = $value;
    }
}
