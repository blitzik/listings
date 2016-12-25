<?php

namespace Listings\Components;

use Nette\Application\BadRequestException;
use Listings\Facades\EmployerFacade;
use Nette\Application\UI\Multiplier;
use App\Components\BaseControl;
use Listings\Employer;
use Users\User;

class EmployersOverviewControl extends BaseControl
{
    public $onSuccessfulEmployerRemoval;


    /** @var IEmployerItemControlFactory */
    private $employerItemControlFactory;

    /** @var EmployerFacade */
    private $employerFacade;


    /** @var Employer[] */
    private $employers;

    /** @var User */
    private $owner;


    public function __construct(
        User $owner,
        EmployerFacade $employerFacade,
        IEmployerItemControlFactory $employerItemControlFactory
    ) {
        $this->owner = $owner;
        $this->employerFacade = $employerFacade;
        $this->employerItemControlFactory = $employerItemControlFactory;
    }


    public function render()
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/employersOverview.latte');

        if ($this->employers === null) {
            $this->employers = $this->employerFacade->findEmployers($this->owner->getId());
        }
        $template->employers = $this->employers;

        $template->render();
    }


    protected function createComponentEmployerItem()
    {
        return new Multiplier(function ($employerId) {
            if ($this->employers === null) {
                $this->employers[$employerId] = $this->employerFacade->getEmployer($employerId);
                if ($this->employers[$employerId] === null) {
                    throw new BadRequestException;
                }
            }

            $comp = $this->employerItemControlFactory
                         ->create($this->employers[$employerId]);

            $comp->onSuccessfulEmployerRemoval[] = function () {
                $this->onSuccessfulEmployerRemoval();

                $this->employers = null;
                $this->redrawControl('overview');
            };


            return $comp;
        });
    }
}


interface IEmployersOverviewControlFactory
{
    /**
     * @param User $owner
     * @return EmployersOverviewControl
     */
    public function create(User $owner);
}