<?php declare(strict_types=1);

namespace Accounts\PublicModule\Presenters;

use Accounts\Components\IForgottenPasswordChangeControlFactory;
use Accounts\Components\IForgottenPasswordFormControlFactory;
use Accounts\Components\ForgottenPasswordChangeControl;
use Accounts\Components\ForgottenPasswordFormControl;
use Common\AuthModule\Presenters\PublicPresenter;
use Common\Components\FlashMessages\FlashMessage;
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


    public function actionRequest(): void
    {
        $this['metaTitle']->setTitle('Obnova hesla');
    }
    
    
    public function renderRequest(): void
    {
    }


    protected function createComponentRequestForm(): ForgottenPasswordFormControl
    {
        $comp = $this->forgottenPasswordFormControlFactory->create();

        return $comp;
    }


    /*
     * --------------------
     * ----- CHANGE -------
     * --------------------
     */


    public function actionChange($email, $token): void
    {
        if ($email === null or $token === null or !Validators::is($email, 'email') or !Validators::is($token, sprintf('unicode:%s', User::LENGTH_TOKEN))) {
            $this->redirect(':Accounts:Public:Auth:logIn');
        }

        $this->account = $this->accountFacade->getUserByEmail($email);
        if ($this->account === null or $this->account->getToken() !== $token) {
            $this->redirect(':Accounts:Public:Auth:logIn');
        }

        $this['metaTitle']->setTitle('Obnova hesla');
    }


    public function renderChange($email, $token): void
    {
    }


    protected function createComponentPasswordChange(): ForgottenPasswordChangeControl
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