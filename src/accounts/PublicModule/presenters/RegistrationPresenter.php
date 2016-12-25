<?php

namespace Accounts\PublicModule\Presenters;

use Accounts\Components\IRegistrationControlFactory;
use App\AuthModule\Presenters\PublicPresenter;

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

        return $comp;
    }
}