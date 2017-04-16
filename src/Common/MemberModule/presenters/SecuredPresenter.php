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


    protected function startup()
    {
        parent::startup();

        if (!$this->user->isLoggedIn()) {
            $this->_backLink = $this->storeRequest();
            $this->redirect(':Accounts:Public:Auth:login');
        }
    }


    protected function beforeRender()
    {
        parent::beforeRender();

        $this->template->_userEntity = $this->user->getIdentity();
        $this->template->_years = TimeUtils::generateYearsForSelection();
        $this->template->_chosenYear = $this->chosenYear;
    }


    /**
     * @param Listing $listing
     */
    protected function setListingPageTitle(Listing $listing)
    {
        $this['pageTitle']->setPageTitle(sprintf('%s %s', TimeUtils::getMonthName($listing->getMonth()), $listing->getYear()));
        if ($listing->getName() !== null) {
            $this['pageTitle']->setJoinedText($listing->getName());
        }
    }


    public function findLayoutTemplateFile()
    {
        if ($this->layout === false) {
            return;
        }

        return __DIR__ . '/templates/@layout.latte';
    }
}