<?php

namespace Mcfedr\Plist\Type;

class PDate implements PType
{
    const FORMAT = 'Y-m-d\TH:i:s\Z';

    /**
     * @var \DateTime|null
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
     * @return \DateTime|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param \DateTime $value
     */
    public function setValue(\DateTime $value = null)
    {
        $this->value = $value;
    }
}
