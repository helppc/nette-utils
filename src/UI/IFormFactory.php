<?php declare(strict_types=1);

namespace HelpPC\NetteUtils\UI;

use Nette\ComponentModel\IContainer;

interface IFormFactory
{

    public function create(bool $csrfProtection = false, ?IContainer $parent = null, ?string $name = null): Form;

}
