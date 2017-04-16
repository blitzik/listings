<?php declare(strict_types=1);

namespace Common\Components\FlashMessages;

use Nette\Localization\ITranslator;

interface IPhrase
{
    public function translate(ITranslator $translator);
}