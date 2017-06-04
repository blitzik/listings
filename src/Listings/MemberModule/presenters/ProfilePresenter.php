<?php declare(strict_types = 1);

namespace Listings\MemberModule\Presenters;

use Listings\Components\IWorkedTimeOverviewControlFactory;
use Common\MemberModule\Presenters\SecuredPresenter;
use Listings\Components\IUserDetailControlFactory;
use Listings\Components\UserDetailControl;
use Accounts\Facades\AccountFacade;

final class ProfilePresenter extends SecuredPresenter
{
    /**
     * @var IWorkedTimeOverviewControlFactory
     * @inject
     */
    public $workedTimeOverviewControlFactory;

    /**
     * @var IUserDetailControlFactory
     * @inject
     */
    public $userDetailControlFactory;

    /**
     * @var AccountFacade
     * @inject
     */
    public $accountFacade;


    public function actionDefault()
    {
        $this['metaTitle']->setTitle('Profil');
        $this['pageTitle']->setPageTitle('Profil');
    }


    public function renderDefault()
    {
    }


    protected function createComponentUserDetail(): UserDetailControl
    {
        $comp = $this->userDetailControlFactory
                     ->create($this->user->getIdentity());

        $comp->onSuccessfulUpdate[] = function () {
            if ($this->isAjax()) {
                $this->redrawControl('username');
            }
        };

        return $comp;
    }


    protected function createComponentWorkedTimeOverview()
    {
        $comp = $this->workedTimeOverviewControlFactory
                     ->create($this->user->getIdentity());

        return $comp;
    }

}
