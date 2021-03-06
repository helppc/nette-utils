<?php declare(strict_types=1);

namespace HelpPC\NetteUtils\UI;

trait TFormFactory
{

    private FormFactory $formFactory;

    public function injectFormFactory(FormFactory $formFactory): void
    {
        $this->formFactory = $formFactory;
    }

    protected function getFormFactory(): FormFactory
    {
        return $this->formFactory;
    }

}
