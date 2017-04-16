<?php declare(strict_types=1);

namespace Common\Components\FlashMessages;

use Nette\Localization\ITranslator;
use Nette\SmartObject;

class Phrase implements IPhrase
{
    use SmartObject;
    
    /** @var array */
    private $parameters;

    /** @var string */
    private $message;

    /** @var null */
    private $count;


    public function __construct(string $message, int $count = null, array $parameters = [])
    {
        $this->message = $message;
        $this->count = $count;
        $this->parameters = $parameters;
    }


    /**
     * @param ITranslator $translator
     * @return string
     */
    public function translate(ITranslator $translator): string
    {
        return $translator->translate($this->message, $this->count, $this->parameters);
    }

}