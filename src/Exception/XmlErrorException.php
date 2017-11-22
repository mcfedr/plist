<?php

namespace Mcfedr\Plist\Exception;

class XmlErrorException extends PlistException
{
    /**
     * @param \libXMLError[] $errors
     */
    public function __construct(array $errors)
    {
        $messages = [];
        foreach ($errors as $error) {
            $level = $this->level($error->level);
            $messages[] = "[$level]{$error->code}: {$error->message} in {$error->file}:L{$error->line}:C{$error->column}";
        }

        parent::__construct(implode('; ', $messages));
    }

    private function level($level)
    {
        switch ($level) {
            case LIBXML_ERR_WARNING:
                return 'Warning';
            case LIBXML_ERR_ERROR:
                return 'Error';
            case LIBXML_ERR_FATAL:
                return 'Fatal';
        }

        return $level;
    }
}
