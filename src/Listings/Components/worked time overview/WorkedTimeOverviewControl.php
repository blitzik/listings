<?php declare(strict_types = 1);

namespace Listings\Components;

use blitzik\Utils\Time;
use Listings\Facades\ListingFacade;
use Common\Components\BaseControl;
use Listings\Utils\Time\ListingTime;
use Users\User;

class WorkedTimeOverviewControl extends BaseControl
{
    /** @var ListingFacade */
    private $listingFacade;

    /** @var User */
    private $userEntity;


    public function __construct(
        User $userEntity,
        ListingFacade $listingFacade
    ) {
        $this->userEntity = $userEntity;
        $this->listingFacade = $listingFacade;
    }


    public function render(): void
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/workedTimeOverview.latte');

        $data = $this->listingFacade->getWorkedTime($this->userEntity->getId())[0];
        if ($data['workedDays'] === null) {
            $data['workedDays'] = 0;
        }
        $data['workedHours'] = new ListingTime($data['workedHours']);

        $template->data = $data;


        $template->render();
    }
}


interface IWorkedTimeOverviewControlFactory
{
    /**
     * @param User $userEntity
     * @return WorkedTimeOverviewControl
     */
    public function create(User $userEntity): WorkedTimeOverviewControl;
}
