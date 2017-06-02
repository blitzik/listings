<?php declare(strict_types=1);

namespace Listings\MemberModule\Presenters;

use Listings\Components\IEmployersOverviewControlFactory;
use Listings\Components\IEmployerFormControlFactory;
use Common\MemberModule\Presenters\SecuredPresenter;
use Listings\Components\EmployersOverviewControl;
use Listings\Components\EmployerFormControl;
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


    public function actionDefault(): void
    {
        $this['metaTitle']->setTitle('Správa zaměstnavatelů');
        $this['pageTitle']->setPageTitle('Správa zaměstnavatelů');
    }


    public function renderDefault(): void
    {
    }


    protected function createComponentNewEmployerForm(): EmployerFormControl
    {
        $comp = $this->employerFormControlFactory->create();

        $comp->onSuccessfulSaving[] = [$this, 'onSuccessfulEmployerCreation'];

        return $comp;
    }


    public function onSuccessfulEmployerCreation(Employer $employer): void
    {
        if (!$this->isAjax()) {
            $this->redirect('this');
        }

        $this['employersOverview']->redrawControl();
    }


    protected function createComponentEmployersOverview(): EmployersOverviewControl
    {
        $comp = $this->employersOverviewControlFactory
                     ->create($this->user->getIdentity());

        $comp->onSuccessfulEmployerRemoval[] = function () {
            if (!$this->isAjax()) {
                $this->redirect('this');
            }
        };

        return $comp;
    }
}