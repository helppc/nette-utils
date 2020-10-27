<?php declare(strict_types=1);

namespace HelpPC\NetteUtils\UI;

use Nette\ComponentModel\IContainer;
use Nette\Localization\ITranslator;
use HelpPC\NetteUtils\Utils\FormValidator;

class FormFactory implements IFormFactory
{

    private ITranslator $translator;

    private FormValidator $validator;

    public function __construct(FormValidator $validator, ITranslator $translator)
    {
        $this->validator = $validator;
        $this->translator = $translator;
        $this->validator->setValidatorMessages();
    }

    public function create(bool $csrfProtection = false, ?IContainer $parent = null, ?string $name = null): Form
    {
        $form = new Form($parent, $name);
        $form->setBootstrapRenderer();
        $form->setTranslator($this->translator);
        $form->addValidator($this->validator);
        if ($csrfProtection === true) {
            $form->addProtection();
        }
        return $form;
    }

}
