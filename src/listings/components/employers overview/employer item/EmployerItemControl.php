<?php

namespace Listings\Components;

use Nette\Forms\Controls\SubmitButton;
use Listings\Facades\EmployerFacade;
use App\Components\BaseControl;
use Nette\Application\UI\Form;
use Listings\Employer;

class EmployerItemControl extends BaseControl
{
    public $onSuccessfulEmployerRemoval;


    /** @var EmployerFacade */
    private $employerFacade;


    /** @var string */
    private $originalTemplatePath = __DIR__ . '/layout.latte';

    /** @var bool */
    private $isRemovalDisplayed = false;

    /** @var Employer */
    private $employer;


    public function __construct(
        Employer $employer,
        EmployerFacade $employerFacade
    ) {
        $this->employer = $employer;
        $this->employerFacade = $employerFacade;
    }


    public function render()
    {
        $template = $this->getTemplate();
        $template->originalTemplatePath = $this->originalTemplatePath;

        if ($this->isRemovalDisplayed === true) {
            $template->setFile(__DIR__ . '/templates/removal.latte');

        } else {
            $template->setFile(__DIR__ . '/templates/employerItem.latte');
        }

        $template->employer = $this->employer;


        $template->render();
    }


    public function handleDisplayRemovalForm()
    {
        $this->isRemovalDisplayed = true;

        $this->refresh('this', ['content']);
    }


    protected function createComponentForm()
    {
        $form = new Form;

        $form->addText('name', $this->employer->getName(), null, Employer::LENGTH_NAME)
                ->setRequired('Zadejte název zaměstnavatele')
                ->setDefaultValue($this->employer->getName());

        $form->addSubmit('save', 'Uložit');

        $form->onSuccess[] = [$this, 'processEmployer'];


        return $form;
    }


    public function processEmployer(Form $form, $values)
    {
        $this->employerFacade->save((array)$values, $this->employer);

        $this->refresh('this', ['content']);

        unset($this['form']);
    }


    protected function createComponentRemovalForm()
    {
        $form = new Form;

        $form->addSubmit('remove', 'Odebrat')
                ->onClick[] = [$this, 'processRemoval'];

        $form->addSubmit('cancel', 'zpět')
                ->onClick[] = [$this, 'processCancel'];


        return $form;
    }


    public function processRemoval(SubmitButton $button)
    {
        $this->employerFacade->remove($this->employer->getId());

        $this->onSuccessfulEmployerRemoval();
    }


    public function processCancel(SubmitButton $button)
    {
        $this->isRemovalDisplayed = false;

        $this->refresh('this', ['content']);
    }

}


interface IEmployerItemControlFactory
{
    /**
     * @param Employer $employer
     * @return EmployerItemControl
     */
    public function create(Employer $employer);
}