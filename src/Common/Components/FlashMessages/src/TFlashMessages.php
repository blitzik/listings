<?php declare(strict_types=1);

namespace Common\Components\FlashMessages;

use Nette\Localization\ITranslator;

trait TFlashMessages
{
    /** @var ITranslator|null */
    private $flashMessagesTranslator;


    /**
     * Saves the message to template, that can be displayed after redirect.
     * @param  string|Phrase
     * @param  string
     * @param int $count
     * @param array $parameters
     * @return FlashMessage
     */
    public function flashMessage($message, $type = 'info', $count = null, array $parameters = []): FlashMessage
    {
        $id = $this->getParameterId('flash');
        $messages = $this->getPresenter()->getFlashSession()->$id;

        if ($message instanceof Phrase) {
            $message = new Phrase($message->message, $message->count, $message->parameters);
        } elseif (!$message instanceof IPhrase) {
            $message = new Phrase($message, $count, $parameters);
        }

        $flash = new FlashMessage($this->getTranslator(), $message);
        $flash->setType($type);

        $messages[] = $flash;

        $this->getTemplate()->flashes = $messages;
        $this->getPresenter()->getFlashSession()->$id = $messages;

        return $flash;
    }


    /**
     * @param ITranslator $translator
     */
    public function injectFlashMessagesTranslator(ITranslator $translator): void
    {
        $this->flashMessagesTranslator = $translator;
    }


    /**
     * @return ITranslator
     */
    public function getTranslator(): ITranslator
    {
        if ($this->flashMessagesTranslator === null) {
            return new NullTranslator();
        }

        return $this->flashMessagesTranslator;
    }
}