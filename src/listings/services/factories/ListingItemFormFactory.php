<?php declare(strict_types=1);

namespace Listings\Services\Factories;

use Listings\Template\Filters\InvoiceTimeFilter;
use Nette\Application\UI\Form;
use Listings\IListingItem;
use Nette\SmartObject;

class ListingItemFormFactory
{
    use SmartObject;


    /**
     * @param IListingItem|null $listingItem
     * @param bool $isAjaxified
     * @return Form
     */
    public function create(IListingItem $listingItem = null, bool $isAjaxified = true)
    {
        $form = new Form;
        if ($isAjaxified) {
            $form->getElementPrototype()->class = 'ajax';
        }

        $form->addText('workStart', 'Začátek')
            ->setRequired('Zadejte začátek směny')
            ->setHtmlId('_work-start')
            ->setDefaultValue('6:00')
            ->addRule(Form::PATTERN, 'Špatný formát času', $this->getTimeRegex());

        $form->addText('workEnd', 'Konec')
            ->setRequired('Zadejte konec směny')
            ->setHtmlId('_work-end')
            ->setDefaultValue('16:00')
            ->addRule(Form::PATTERN, 'Špatný formát času', $this->getTimeRegex());

        $form->addText('locality', 'Místo pracoviště')
            ->setRequired('Zadejte místo pracoviště')
            ->setAttribute('list', '_work-locality');

        if ($listingItem !== null) {
            $this->fillForm($form, $listingItem);
        }

        $form->addSubmit('save', 'Uložit');

        $form->addProtection();

        return $form;
    }


    private function fillForm(Form $form, IListingItem $listingItem)
    {
        $form['workStart']->setDefaultValue(InvoiceTimeFilter::convert($listingItem->getWorkStart(), true));
        $form['workEnd']->setDefaultValue(InvoiceTimeFilter::convert($listingItem->getWorkEnd(), true));
        $form['locality']->setDefaultValue($listingItem->getLocality());
    }


    public function getTimeRegex()
    {
        return '^(0?[0-9]|1[0-9]|2[0-3]):[03]0$';
    }
}