<?php declare(strict_types=1);

namespace Common\MemberModule\Presenters;

use Common\Presenters\AppPresenter;
use Listings\Services\TimeUtils;
use Listings\Listing;

abstract class SecuredPresenter extends AppPresenter
{
    /** @var string */
    protected $chosenYear;

    /** @var string */
    protected $_backLink;


    protected function startup(): void
    {
        parent::startup();

        if (!$this->user->isLoggedIn()) {
            $this->_backLink = $this->storeRequest();
            $this->redirect(':Accounts:Public:Auth:logIn');
        }
    }


    protected function beforeRender(): void
    {
        parent::beforeRender();

        $this->template->_userEntity = $this->user->getIdentity();
        $this->template->_years = TimeUtils::generateYearsForSelection();
        $this->template->_chosenYear = $this->chosenYear;
    }


    protected function setListingPageTitle(Listing $listing, bool $isLink = false): string
    {
        $title = sprintf('%s %s', TimeUtils::getMonthName($listing->getMonth()), $listing->getYear());
        $this['pageTitle']->setPageTitle($title);
        if ($listing->getName() !== null) {
            $this['pageTitle']->setJoinedText($listing->getName());
        }

        if ($isLink === true) {
            $this['pageTitle']->makeItLink($this->link(':Listings:Member:ListingDetail:default', ['id' => $listing->getId()]));
        }

        return sprintf('%s%s', $title, $listing->getName() !== null ? (' - ' . $listing->getName()) : null);
    }


    public function findLayoutTemplateFile(): ?string
    {
        if ($this->layout === false) {
            return null;
        }

        return __DIR__ . '/templates/@layout.latte';
    }
}