<?php declare(strict_types=1);

namespace Listings\MemberModule\Presenters;

use Listings\Components\IListingsOverviewControlFactory;
use Common\MemberModule\Presenters\SecuredPresenter;
use Listings\Components\ListingsOverviewControl;
use Listings\Services\TimeUtils;

final class DashboardPresenter extends SecuredPresenter
{
    /**
     * @var IListingsOverviewControlFactory
     * @inject
     */
    public $listingsOverviewControlFactory;


    public function actionDefault($year): void
    {
        if ($year !== null and !array_key_exists($year, TimeUtils::generateYearsForSelection())) {
            $this->redirect(':Listings:Member:Dashboard:default', ['year' => null]);
        }

        if ($year === null) {
            $year = date('Y');
        }
        $this->chosenYear = $year;

        $this['metaTitle']->setTitle(sprintf('Přehled výčetek za rok %s', $year));
        $this['pageTitle']->setPageTitle(sprintf('Rok %s', $year));
    }


    public function renderDefault($year): void
    {
    }


    protected function createComponentListingsOverview(): ListingsOverviewControl
    {
        $comp = $this->listingsOverviewControlFactory->create($this->chosenYear);

        return $comp;
    }

}