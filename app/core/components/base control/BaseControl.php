<?php

namespace App\Components;

use App\Components\FlashMessages\TFlashMessages;
use Nette\Application\UI\Control;
use Nette\Security\IAuthorizator;

abstract class BaseControl extends Control
{
    use TFlashMessages;


    /** @var  IFlashMessagesControlFactory */
    protected $flashMessagesFactory;

    /** @var IAuthorizator */
    protected $authorizator;

    /** @var \Nette\Security\User */
    protected $user;


    public function setAuthorizator(IAuthorizator $authorizator)
    {
        $this->authorizator = $authorizator;
    }


    public function setUser(\Nette\Security\User $user)
    {
        $this->user = $user;
    }
    

    /**
     * @param IFlashMessagesControlFactory $factory
     */
    public function injectFlashMessagesFactory(IFlashMessagesControlFactory $factory)
    {
        $this->flashMessagesFactory = $factory;
    }


    protected function createComponentFlashMessages()
    {
        $comp = $this->flashMessagesFactory->create();

        return $comp;
    }


    public function refresh($redirect, array $snippets = null)
    {
        if ($this->presenter->isAjax()) {
            if ($snippets === null or empty($snippets)) {
                $this->redrawControl();
            } else {
                foreach ($snippets as $snippet) {
                    $this->redrawControl($snippet);
                }
            }
        }

        if (!$this->presenter->isAjax()) {
            $this->redirect($redirect);
        }
    }


}