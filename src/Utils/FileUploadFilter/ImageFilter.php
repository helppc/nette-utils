<?php declare(strict_types=1);

namespace HelpPC\NetteUtils\Utils\FileUploadFilter;

class ImageFilter extends BaseFilter
{

    /**
     * @return array<string,string>
     */
    protected function getMimeTypes(): array
    {
        return [
            'image/png' => 'png',
            'image/pjpeg' => 'jpeg',
            'image/jpeg' => 'jpg',
            'image/gif' => 'gif',
        ];
    }

}
