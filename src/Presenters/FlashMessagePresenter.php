<?php declare(strict_types=1);

namespace HelpPC\NetteUtils\Presenters;

use stdClass;

interface FlashMessagePresenter
{
    public const FL_SUCCESS = 'success';
    public const FL_WARNING = 'warning';
    public const FL_DANGER = 'danger';
    public const FL_ERROR = 'danger';
    public const FL_INFO = 'info';

    public function flashMessage(string $message, string $type = self::FL_INFO): stdClass;

}