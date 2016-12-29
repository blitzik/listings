<?php

namespace Accounts\PublicModule\Presenters;

use Accounts\Components\IForgottenPasswordChangeControlFactory;
use Accounts\Components\IForgottenPasswordFormControlFactory;
use App\AuthModule\Presenters\PublicPresenter;
use App\Components\FlashMessages\FlashMessage;
use Accounts\Facades\AccountFacade;
use Nette\Utils\Validators;
use Users\User;

final class ForgottenPasswordPresenter extends PublicPresenter
{
    /**
     * @var IForgottenPasswordChangeControlFactory
     * @inject
     */
    public $forgottenPasswordChangeControlFactory;

    /**
     * @var IForgottenPasswordFormControlFactory
     * @inject
     */
    public $forgottenPasswordFormControlFactory;

    /**
     * @var AccountFacade
     * @inject
     */
    public $accountFacade;


    /** @var User */
    private $account;


    public function actionRequest()
    {
        $this['metaTitle']->setTitle('Obnova hesla');
    }
    
    
    public function renderRequest()
    {
        
    }


    protected function createComponentRequestForm()
    {
        $comp = $this->forgottenPasswordFormControlFactory->create();

        return $comp;
    }


    /*
     * --------------------
     * ----- CHANGE -------
     * --------------------
     */


    public function actionChange($email, $token)
    {
        if ($email === null or $token === null or !Validators::is($email, 'email') or !Validators::is($token, sprintf('unicode:%s', User::LENGHT_TOKEN))) {
            $this->redirect(':Accounts:Public:Auth:logIn');
        }

        $this->account = $this->accountFacade->getUserByEmail($email);
        if ($this->account === null or $this->account->getToken() !== $token) {
            $this->redirect(':Accounts:Public:Auth:logIn');
        }

        $this['metaTitle']->setTitle('Obnova hesla');
    }


    public function renderChange($email, $token)
    {
    }


    protected function createComponentPasswordChange()
    {
        $comp = $this->forgottenPasswordChangeControlFactory
                     ->create($this->account);

        $comp->onSuccessfulPasswordChange[] = function () {
            $this->flashMessage('Heslo bylo změněno', FlashMessage::SUCCESS);
            $this->redirect(':Accounts:Public:Auth:logIn');
        };

        return $comp;
    }

}