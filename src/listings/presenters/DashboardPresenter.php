<?php

namespace Listings\Presenters;

use Listings\Components\IListingsOverviewControlFactory;
use App\MemberModule\Presenters\SecuredPresenter;
use Listings\Services\TimeUtils;

final class DashboardPresenter extends SecuredPresenter
{
    /**
     * @var IListingsOverviewControlFactory
     * @inject
     */
    public $listingsOverviewControlFactory;


    public function actionDefault($year)
    {
        if ($year !== null and !array_key_exists($year, TimeUtils::generateYearsForSelection())) {
            $this->redirect(':Listings:Dashboard:default', ['year' => null]);
        }

        if ($year === null) {
            $year = date('Y');
        }
        $this->chosenYear = $year;

        $this['metaTitle']->setTitle(sprintf('Přehled výčetek za rok %s', $year));
        $this['pageTitle']->setPageTitle(sprintf('Rok %s', $year));
    }


    public function renderDefault($year)
    {
    }


    protected function createComponentListingsOverview()
    {
        $comp = $this->listingsOverviewControlFactory->create($this->chosenYear);

        return $comp;
    }

}