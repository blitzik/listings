<?php declare(strict_types = 1);

namespace Listings\MemberModule\Presenters;

use Listings\Components\IListingSettingsControlFactory;
use Common\MemberModule\Presenters\SecuredPresenter;

final class SettingsPresenter extends SecuredPresenter
{
    /**
     * @var IListingSettingsControlFactory
     * @inject
     */
    public $listingSettingsControlFactory;


    public function actionDefault()
    {
        $this['pageTitle']->setPageTitle('Nastavení');
        $this['metaTitle']->setTitle('Nastavení');
    }


    public function renderDefault()
    {
    }


    protected function createComponentListingSettings()
    {
        $comp = $this->listingSettingsControlFactory->create($this->user->getIdentity());

        return $comp;
    }
}