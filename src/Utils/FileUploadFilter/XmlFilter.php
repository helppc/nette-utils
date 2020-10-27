<?php declare(strict_types=1);

namespace HelpPC\NetteUtils\Utils\FileUploadFilter;

class XmlFilter extends BaseFilter
{

    /**
     * @return string[]
     */
    protected function getMimeTypes(): array
    {
        return [
            'text/xml' => 'xml',
        ];
    }

}
