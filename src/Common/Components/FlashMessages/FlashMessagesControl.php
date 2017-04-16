<?php declare(strict_types=1);

namespace Common\Components;

use Nette\Application\UI\Control;

class FlashMessagesControl extends Control
{
    public function render()
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/flashMessages.latte');

        $template->flashes = $this->getParent()->getTemplate()->flashes;

        $template->render();
    }
}


interface IFlashMessagesControlFactory
{
    /**
     * @return FlashMessagesControl
     */
    public function create();
}