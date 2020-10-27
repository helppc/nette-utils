<?php declare(strict_types=1);

namespace HelpPC\NetteUtils\Enum;

use Consistence\Enum\Enum;

class GenderEnum extends Enum
{

    public const EMPTY = 'n/a';
    public const MALE = 'male';
    public const FEMALE = 'female';

    /**
     * @return array<string,string>
     */
    public static function getAvailableValuesForSelect(): array
    {
        return [
            self::EMPTY => 'helppcUtils.option.unknown',
            self::MALE => 'helppcUtils.option.male',
            self::FEMALE => 'helppcUtils.option.female',
        ];
    }

    public function getTranslatedValue(): string
    {
        return self::getAvailableValuesForSelect()[$this->getValue()];
    }

}
