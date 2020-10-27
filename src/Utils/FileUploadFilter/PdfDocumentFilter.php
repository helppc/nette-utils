<?php declare(strict_types=1);

namespace HelpPC\NetteUtils\Utils\FileUploadFilter;

class PdfDocumentFilter extends BaseFilter
{

    /**
     * @return array<string,string>
     */
    protected function getMimeTypes(): array
    {
        return [
            'application/pdf' => '.pdf',
        ];
    }

}
