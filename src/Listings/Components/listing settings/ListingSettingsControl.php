<?php declare(strict_types = 1);

namespace Listings\Components;

use Listings\Services\Factories\ListingSettingsFormFactory;
use Listings\Exceptions\Runtime\WorkedHoursRangeException;
use Listings\Exceptions\Runtime\LunchHoursRangeException;
use Listings\Exceptions\Runtime\WorkedHoursException;
use Listings\Exceptions\Runtime\LunchHoursException;
use Common\Components\FlashMessages\FlashMessage;
use Listings\Facades\ListingFacade;
use Common\Components\BaseControl;
use Nette\Application\UI\Form;
use Listings\ListingSettings;
use Users\User;

class ListingSettingsControl extends BaseControl
{
    /** @var ListingSettingsFormFactory */
    private $listingSettingFormFactory;

    /** @var ListingFacade */
    private $listingFacade;


    /** @var User */
    private $userEntity;

    /** @var ListingSettings */
    private $settings;


    public function __construct(
        User $userEntity,
        ListingFacade $listingFacade,
        ListingSettingsFormFactory $listingSettingFormFactory
    ) {
        $this->userEntity = $userEntity;
        $this->listingSettingFormFactory = $listingSettingFormFactory;
        $this->listingFacade = $listingFacade;
    }


    public function render(): void
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/listingSettings.latte');

        if ($this->settings === null) {
            $this->settings = $this->listingFacade->getListingSettings($this->userEntity);
        }
        $template->workedHours = $this->settings->getWorkedHours()->getTimeWithComma();


        $template->render();
    }


    protected function createComponentListingSettingsForm()
    {
        if ($this->settings === null) {
            $this->settings = $this->listingFacade->getListingSettings($this->userEntity);
        }
        $form = $this->listingSettingFormFactory->create($this->settings);

        $form->onSuccess[] = [$this, 'processSettings'];

        return $form;
    }


    public function processSettings(Form $form, $values)
    {
        try {
            $this->settings->changeHours($values['workStart'], $values['workEnd'], $values['lunchStart'],$values['lunchEnd']);
            $this->settings->setItemType($values['itemType']);

            $this->listingFacade->saveListingSettings($this->settings);
            $this->flashMessage('Nastavení bylo úspěšně uloženo.', FlashMessage::SUCCESS);

        } catch (WorkedHoursRangeException $e) {
            $this->flashMessage('Nastavení nelze uložit. Pracovní doba nemůže skončit dříve, než začala.', FlashMessage::WARNING);

        } catch (WorkedHoursException $e) {
            $this->flashMessage('Nastavení nelze uložit. Musíte mít odpracováno alespoň 30 minut.', FlashMessage::WARNING);

        } catch (LunchHoursRangeException $e) {
            $this->flashMessage('Nastavení nelze uložit. Oběd nemůže končit dříve než začal.', FlashMessage::WARNING);

        } catch (LunchHoursException $e) {
            $this->flashMessage('Nastavení nelze uložit. Začátek a konec oběda se musí nacházet v rozsahu směny.', FlashMessage::WARNING);
        }
    }
}


interface IListingSettingsControlFactory
{
    /**
     * @param User $userEntity
     * @return ListingSettingsControl
     */
    public function create(User $userEntity): ListingSettingsControl;
}