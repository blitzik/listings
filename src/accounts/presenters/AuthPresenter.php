<?php

namespace Accounts\Presenters;

use App\AuthModule\Presenters\PublicPresenter;
use Accounts\Components\ILoginControlFactory;

final class AuthPresenter extends PublicPresenter
{
    /**
     * @var ILoginControlFactory
     * @inject
     */
    public $loginControlFactory;


    public function actionLogin()
    {
        if ($this->user->isLoggedIn()) {
            $this->redirect(':Listings:Dashboard:default');
        }
    }


    public function renderLogin()
    {
    }


    protected function createComponentLogin()
    {
        $comp = $this->loginControlFactory->create();

        $comp->onSuccessfulLogin[] = function () {
            $this->redirect(':Listings:Dashboard:default');
        };

        return $comp;
    }


    /*
     * --------------------
     * ----- LOGOUT -------
     * --------------------
     */


    public function actionLogOut()
    {
        $this->user->logout();
        $this->redirect(':Accounts:Auth:logIn');
    }

}