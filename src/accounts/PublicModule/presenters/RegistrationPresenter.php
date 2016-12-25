<?php

namespace Accounts\PublicModule\Presenters;

use Accounts\Components\IRegistrationControlFactory;
use App\AuthModule\Presenters\PublicPresenter;
use App\Components\FlashMessages\FlashMessage;

final class RegistrationPresenter extends PublicPresenter
{
    /**
     * @var IRegistrationControlFactory
     * @inject
     */
    public $registrationControlFactory;


    public function actionDefault()
    {

    }


    public function renderDefault()
    {

    }


    protected function createComponentRegistration()
    {
        $comp = $this->registrationControlFactory->create();

        $comp->onSuccessfulAccountCreation[] = function () {
            $this->flashMessage('Účet byl vytvořen. Můžete se přihlásit.', FlashMessage::SUCCESS);
            $this->redirect(':Accounts:Public:Auth:logIn');
        };

        return $comp;
    }
}