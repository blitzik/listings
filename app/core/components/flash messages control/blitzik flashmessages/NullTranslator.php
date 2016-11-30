<?php

namespace App\Components\FlashMessages;

use Nette\Localization\ITranslator;
use Nette\Localization\message;
use Nette\Localization\plural;
use Nette\SmartObject;

class NullTranslator implements ITranslator
{
    use SmartObject;
    
    /**
     * Translates the given string.
     * @param  string   message
     * @param  int      plural count
     * @return string
     */
    function translate($message, $count = null)
    {
        return $message;
    }

}