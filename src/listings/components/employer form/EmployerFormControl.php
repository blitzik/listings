<?php

namespace Listings\Components;

use App\Components\BaseControl;
use Listings\Employer;
use Listings\Facades\EmployerFacade;
use Nette\Application\UI\Form;

class EmployerFormControl extends BaseControl
{
    public $onSuccessfulSaving;


    /** @var EmployerFacade */
    private $employerFacade;


    /** @var Employer */
    private $employer;


    public function __construct(
        EmployerFacade $employerFacade
    ) {
        $this->employerFacade = $employerFacade;
    }


    /**
     * @param Employer $employer
     */
    public function updateEmployer(Employer $employer)
    {
        $this->employer = $employer;
    }


    public function render()
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/employerForm.latte');



        $template->render();
    }


    protected function createComponentForm()
    {
        $form = new Form;
        $form->getElementPrototype()->class = 'ajax';

        $form->addText('name', 'Nový zaměstnavatel', null, Employer::LENGTH_NAME)
                ->setRequired('Zadejte název zaměstnavatele');

        $form->addSubmit('save', 'Uložit');

        $form->onSuccess[] = [$this, 'processEmployer'];


        return $form;
    }


    public function processEmployer(Form $form, $values)
    {
        $values['user'] = $this->user->getId();
        $employer = $this->employerFacade->save((array)$values, $this->employer);

        $this->onSuccessfulSaving($employer);
    }
}


interface IEmployerFormControlFactory
{
    /**
     * @return EmployerFormControl
     */
    public function create();
}