<?php declare(strict_types=1);

namespace HelpPC\NetteUtils\Utils\FileUploadFilter;

class HtmlFilter extends BaseFilter
{

    /**
     * @return string[]
     */
    protected function getMimeTypes(): array
    {
        return [
            'text/plain' => 'txt',
            'text/latte' => 'latte',
            'text/html' => 'html',
            'text/htm' => 'htm',
        ];
    }

}
