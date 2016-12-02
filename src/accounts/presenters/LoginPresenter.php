<?php

namespace Accounts\Presenters;

use App\AuthModule\Presenters\PublicPresenter;
use Accounts\Components\ILoginControlFactory;

final class LoginPresenter extends PublicPresenter
{
    /**
     * @var ILoginControlFactory
     * @inject
     */
    public $loginControlFactory;


    public function actionDefault()
    {
    }


    public function renderDefault()
    {
    }


    protected function createComponentLogin()
    {
        $comp = $this->loginControlFactory->create();

        return $comp;
    }
}