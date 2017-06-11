<?php declare(strict_types=1);

namespace Listings\Services\Factories;

use Listings\ListingSettings;
use Listings\Template\Filters\InvoiceTimeFilter;
use Nette\Application\UI\Form;
use Listings\IListingItem;
use Nette\SmartObject;

class ListingItemFormFactory
{
    use SmartObject;


    /**
     * @param ListingSettings $setting
     * @param IListingItem|null $listingItem
     * @param bool $isAjaxified
     * @return Form
     */
    public function create(ListingSettings $setting, IListingItem $listingItem = null, bool $isAjaxified = true): Form
    {
        $form = new Form;
        if ($isAjaxified) {
            $form->getElementPrototype()->class = 'ajax';
        }

        $form->addText('workStart', 'Začátek')
            ->setRequired('Zadejte začátek směny')
            ->setHtmlId('_work-start')
            ->setDefaultValue(InvoiceTimeFilter::convert($setting->getWorkStart(), true))
            ->addRule(Form::PATTERN, 'Špatný formát času', $this->getTimeRegex());

        $form->addText('workEnd', 'Konec')
            ->setRequired('Zadejte konec směny')
            ->setHtmlId('_work-end')
            ->setDefaultValue(InvoiceTimeFilter::convert($setting->getWorkEnd(), true))
            ->addRule(Form::PATTERN, 'Špatný formát času', $this->getTimeRegex());

        $form->addText('locality', 'Místo pracoviště', null, IListingItem::LENGTH_LOCALITY)
            ->setRequired('Zadejte místo pracoviště')
            ->addRule(Form::MAX_LENGTH, 'Do místa pracoviště lze zadat max. %d znaků.', IListingItem::LENGTH_LOCALITY)
            ->setAttribute('list', '_work-locality');

        if ($listingItem !== null) {
            $this->fillForm($form, $listingItem);
        }

        $form->addSubmit('save', 'Uložit');

        $form->addProtection();

        return $form;
    }


    private function fillForm(Form $form, IListingItem $listingItem): void
    {
        $form['workStart']->setDefaultValue(InvoiceTimeFilter::convert($listingItem->getWorkStart(), true));
        $form['workEnd']->setDefaultValue(InvoiceTimeFilter::convert($listingItem->getWorkEnd(), true));
        $form['locality']->setDefaultValue($listingItem->getLocality());
    }


    public function getTimeRegex(): string
    {
        return '^(0?[0-9]|1[0-9]|2[0-3]):[03]0$';
    }
}