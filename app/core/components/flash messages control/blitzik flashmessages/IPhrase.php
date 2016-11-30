<?php

namespace App\Components\FlashMessages;

use Nette\Localization\ITranslator;

interface IPhrase
{
    public function translate(ITranslator $translator);
}