<?php declare(strict_types=1);

namespace Common\Components\FlashMessages;

use Nette\Localization\ITranslator;
use Nette\SmartObject;

/**
 * @property string $type
 * @property string $message
 */
class FlashMessage
{
    use SmartObject;
    
    
    const INFO = 'info';
    const SUCCESS = 'success';
    const WARNING = 'warning';
    const ERROR = 'error';


    /** @var ITranslator */
    private $translator;

    /** @var string */
    private $message;

    /** @var Phrase */
    private $phrase;

    /** @var string */
    private $type;


    public function __construct(ITranslator $translator, IPhrase $phrase)
    {
        $this->translator = $translator;
        $this->phrase = $phrase;
    }


    public function setType(string $type)
    {
        $this->type = $type;
    }


    public function getType(): string
    {
        return $this->type;
    }


    /**
     * @return null|string
     */
    public function getMessage()
    {
        if ($this->message === null and $this->translator !== null) {
            $this->message = $this->phrase->translate($this->translator);
        }

        return $this->message;
    }


    public function __sleep()
    {
        $this->message = $this->getMessage();
        return ['message', 'type'];
    }
}