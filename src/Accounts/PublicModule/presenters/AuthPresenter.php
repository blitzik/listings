<?php declare(strict_types=1);

namespace Accounts\PublicModule\Presenters;

use Common\AuthModule\Presenters\PublicPresenter;
use Accounts\Components\ILoginControlFactory;
use Accounts\Components\LoginControl;

final class AuthPresenter extends PublicPresenter
{
    /**
     * @var ILoginControlFactory
     * @inject
     */
    public $loginControlFactory;


    public function actionLogIn(): void
    {
        if ($this->user->isLoggedIn()) {
            $this->redirect(':Listings:Member:Dashboard:default');
        }

        $this['metaTitle']->setTitle('Přihlášení');
    }


    public function renderLogIn(): void
    {
    }


    protected function createComponentLogIn(): LoginControl
    {
        $comp = $this->loginControlFactory->create();

        $comp->onSuccessfulLogin[] = function () {
            $this->redirect(':Listings:Member:Dashboard:default');
        };

        return $comp;
    }


    /*
     * --------------------
     * ----- LOGOUT -------
     * --------------------
     */


    public function actionLogOut(): void
    {
        $this->user->logout(true);
        $this->redirect(':Accounts:Public:Auth:logIn');
    }

}