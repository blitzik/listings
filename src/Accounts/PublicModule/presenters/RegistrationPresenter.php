<?php declare(strict_types=1);

namespace Accounts\PublicModule\Presenters;

use Accounts\Components\IRegistrationControlFactory;
use Common\AuthModule\Presenters\PublicPresenter;
use Common\Components\FlashMessages\FlashMessage;
use Accounts\Components\RegistrationControl;

final class RegistrationPresenter extends PublicPresenter
{
    /**
     * @var IRegistrationControlFactory
     * @inject
     */
    public $registrationControlFactory;


    public function actionDefault(): void
    {
        $this['metaTitle']->setTitle('Registrace');
    }


    public function renderDefault(): void
    {
    }


    protected function createComponentRegistration(): RegistrationControl
    {
        $comp = $this->registrationControlFactory->create();

        $comp->onSuccessfulAccountCreation[] = function () {
            $this->flashMessage('Účet byl vytvořen. Můžete se přihlásit.', FlashMessage::SUCCESS);
            $this->redirect(':Accounts:Public:Auth:logIn');
        };

        return $comp;
    }
}