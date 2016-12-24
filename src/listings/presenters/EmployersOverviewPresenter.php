<?php

namespace Listings\Presenters;

use Listings\Components\IEmployersOverviewControlFactory;
use Listings\Components\IEmployerFormControlFactory;
use App\MemberModule\Presenters\SecuredPresenter;
use Listings\Employer;

final class EmployersOverviewPresenter extends SecuredPresenter
{
    /**
     * @var IEmployersOverviewControlFactory
     * @inject
     */
    public $employersOverviewControlFactory;

    /**
     * @var IEmployerFormControlFactory
     * @inject
     */
    public $employerFormControlFactory;


    public function actionDefault()
    {
        $this['metaTitle']->setTitle('Správa zaměstnavatelů');
        $this['pageTitle']->setPageTitle('Správa zaměstnavatelů');
    }


    public function renderDefault()
    {

    }


    protected function createComponentNewEmployerForm()
    {
        $comp = $this->employerFormControlFactory->create();

        $comp->onSuccessfulSaving[] = [$this, 'onSuccessfulEmployerCreation'];

        return $comp;
    }


    public function onSuccessfulEmployerCreation(Employer $employer)
    {
        if (!$this->isAjax()) {
            $this->redirect('this');
        }

        $this['employersOverview']->redrawControl();
    }


    protected function createComponentEmployersOverview()
    {
        $comp = $this->employersOverviewControlFactory
                     ->create($this->user->getIdentity());

        return $comp;
    }
}