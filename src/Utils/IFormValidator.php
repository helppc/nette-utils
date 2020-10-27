<?php declare(strict_types=1);

namespace HelpPC\NetteUtils\Utils;

use Nette\Forms\IControl;

interface IFormValidator
{

    public function validateIco(IControl $control): bool;

    public function validateUrl(IControl $control): bool;

    public function validateRc(IControl $control): bool;

    public function validateVatNumber(IControl $control, bool $params = false): bool;

}
