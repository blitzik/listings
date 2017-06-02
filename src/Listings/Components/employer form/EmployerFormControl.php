<?php declare(strict_types=1);

namespace Listings\Components;

use Listings\Facades\EmployerFacade;
use Common\Components\BaseControl;
use Nette\Application\UI\Form;
use Listings\Employer;

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
    public function updateEmployer(Employer $employer): void
    {
        $this->employer = $employer;
    }


    public function render(): void
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/employerForm.latte');



        $template->render();
    }


    protected function createComponentForm(): Form
    {
        $form = new Form;
        $form->getElementPrototype()->class = 'ajax';

        $form->addText('name', 'Nový zaměstnavatel', null, Employer::LENGTH_NAME)
                ->setRequired('Zadejte název zaměstnavatele')
                ->addRule(Form::MAX_LENGTH, 'Lze zadat max. %d znaků', Employer::LENGTH_NAME);

        $form->addSubmit('save', 'Uložit');

        $form->onSuccess[] = [$this, 'processEmployer'];

        $form->addProtection();


        return $form;
    }


    public function processEmployer(Form $form, $values): void
    {
        $values['user'] = $this->user->getId();
        $employer = $this->employerFacade->save((array)$values, $this->employer);

        $this->onSuccessfulSaving($employer);

        unset($this['form']);
        $this->redrawControl('form');
    }
}


interface IEmployerFormControlFactory
{
    /**
     * @return EmployerFormControl
     */
    public function create(): EmployerFormControl;
}