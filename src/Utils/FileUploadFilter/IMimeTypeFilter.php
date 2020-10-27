<?php declare(strict_types=1);

namespace HelpPC\NetteUtils\Utils\FileUploadFilter;

use Nette\Forms\Controls\UploadControl;

interface IMimeTypeFilter
{

    public function checkType(UploadControl $uploadControl): bool;

    public function getAllowedTypes(): string;

}
