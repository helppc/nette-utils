<?php declare(strict_types=1);

namespace HelpPC\NetteUtils\UI;

use Contributte\Translation\Translator;
use Nette;
use Nette\Forms\Controls;
use Nette\Forms\Controls\TextInput;
use Nette\Forms\Rendering\DefaultFormRenderer;
use Nette\InvalidArgumentException;
use RadekDostal\NetteComponents\DateTimePicker\TbDatePicker;
use RadekDostal\NetteComponents\DateTimePicker\TbDateTimePicker;
use Symfony\Component\Intl\Countries;
use HelpPC\NetteUtils\Enum\GenderEnum;
use HelpPC\NetteUtils\Presenters\FlashMessagePresenter;
use HelpPC\NetteUtils\Utils\FileUploadFilter\IMimeTypeFilter;
use HelpPC\NetteUtils\Utils\IFormValidator;

/**
 * Class Form
 *
 * @method TbDateTimePicker addTbDateTimePicker(string $name, string $label)
 * @method TbDatePicker addTbDatePicker(string $name, string $label)
 * @method Translator getTranslator()
 */
class Form extends \Nette\Application\UI\Form
{

    private IFormValidator $validator;

    private bool $validatorSetted = false;

    public function __construct(?Nette\ComponentModel\IContainer $parent = null, ?string $name = null)
    {
        parent::__construct($parent, $name);
        $this->onError[] = function (Form $form): void {
            if ($form->hasErrors() === false) {
                return;
            }
            foreach ($form->getErrors() as $error) {
                $this->getPresenter()->flashMessage($error, FlashMessagePresenter::FL_DANGER);
            }
        };
    }

    public function addValidator(IFormValidator $validator): void
    {
        $this->validatorSetted = true;
        $this->validator = $validator;
    }

    public function addTextWithPlaceholder(string $name, ?string $label = null, ?int $cols = null, ?int $maxLength = null): Controls\TextInput
    {
        $control = parent::addText($name, $label, $cols, $maxLength);
        $control->getControlPrototype()->setAttribute('placeholder', $label);
        return $control;
    }

    public function getValidator(): IFormValidator
    {
        if ($this->validatorSetted === false) {
            throw new InvalidArgumentException();
        }
        return $this->validator;
    }

    public function addFilteredUpload(string $name, string $label, IMimeTypeFilter $filter): Controls\UploadControl
    {
        return $this->addUpload($name, $label)
            ->addRule([$filter, 'checkType'], 'helppcUtils.input.validationError.unsupportedFile', [$filter->getAllowedTypes()]);
    }

    public function addFilteredMultiUpload(string $name, string $label, IMimeTypeFilter $filter): Controls\UploadControl
    {
        $uploader = $this->addMultiUpload($name, $label)
            ->addRule([$filter, 'checkType'], 'helppcUtils.input.validationError.unsupportedFiles', [$filter->getAllowedTypes()]);

        return $uploader;
    }

    public function addUrl(string $name, string $label, string $message = 'helppcUtils.input.validationError.website'): TextInput
    {
        return parent::addText($name, $label)
            ->setRequired(false)
            ->addRule([$this->getValidator(), 'validateUrl'], $message);
    }

    public function addPassword(string $name, $label = null, ?int $cols = null, ?int $maxLength = null): TextInput
    {
        return parent::addPassword($name, $label, $cols, $maxLength)
            ->addRule(self::LENGTH, 'helppcUtils.input.rule.passwordLength', [5, 60]);
    }

    public function addDateTime(string $name, ?string $label = null, string $format = 'j. n. Y H:i', string $jsFormat = 'D. M. YYYY HH:mm'): TbDateTimePicker
    {
        /** @var TbDateTimePicker $control */
        $control = $this->addTbDateTimePicker($name, $label);
        $control->getControlPrototype()->addAttributes(
            [
                'data-type' => 'datetime',
                'data-singledatepicker' => 'true',
                'data-timepicker24hour' => 'true',
                'data-timepicker' => 'true',
                'data-autoapply' => 'true',
                'data-showdropdowns' => 'true',
                'data-format' => $jsFormat,
            ]
        );
        return $control->setFormat($format);
    }

    public function addDate(string $name, ?string $label = null, string $format = 'j. n. Y', string $jsFormat = 'd. m. yyyy'): TbDatePicker
    {
        /** @var TbDatePicker $control */
        $control = $this->addTbDatePicker($name, $label);
        //$control->setHtmlType('date');
        $control->getControlPrototype()->addAttributes(
            [
                'data-type' => 'date',
                'data-provide' => 'datepicker',
                'data-date-orientation' => 'bottom',
                'data-date-today-highlight' => 'true',
                'data-date-autoclose' => 'true',
                'data-date-format' => $jsFormat,
            ]
        );
        return $control->setFormat($format);
    }

    public function addPasswords(string $name, ?string $oldPasswordLabel, string $newPasswordLabel, string $errorMessage = 'Password doesn\'t match.'): TextInput
    {
        $password = self::addPassword($name, $oldPasswordLabel);
        $passwordAgain = self::addPassword($name . 'Again', $newPasswordLabel);
        $password->addRule(self::EQUAL, $errorMessage, $passwordAgain);
        $passwordAgain->addRule(self::EQUAL, $errorMessage, $password);
        return $password;
    }

    public function addValidationEmail(
        string $name,
        ?string $label = null,
        bool $required = true,
        bool $checkMX = false
    ): TextInput
    {
        /** @var callable $rule */
        $rule = [$this->getValidator(), 'validateEmail'];
        $control = parent::addText($name, $label)
            ->addRule($rule, 'helppcUtils.input.validationError.email', $checkMX);
        if ($required) {
            $control->setRequired('helppcUtils.input.required.email');
        }
        return $control;
    }

    public function addRegexpText(
        string $name,
        ?string $label = null,
        bool $required = true,
        string $errorMessage = 'helppcUtils.input.validationError.regexp',
        string $pattern = '/^.{5}-.{2}-.{3}$/'
    ): TextInput
    {
        /** @var callable $rule */
        $rule = [$this->getValidator(), 'validateByRegexp'];
        $control = parent::addText($name, $label)
            ->addRule($rule, $errorMessage, $pattern);
        if ($required) {
            $control->setRequired('helppcUtils.input.required.regexp');
        }
        return $control;
    }

    public function addGender(string $name, string $label = 'helppcUtils.input.gender'): Controls\SelectBox
    {
        return parent::addSelect($name, $label, GenderEnum::getAvailableValuesForSelect());
    }

    public function addVatNumber(string $name, string $label, bool $params = false, string $message = 'helppcUtils.input.validationError.vatNumber'): TextInput
    {
        return parent::addText($name, $label)
            ->setRequired(false)
            ->addRule([$this->getValidator(), 'validateVatNumber'], $message, $params);
    }

    public function addCountries(string $name, string $label): Controls\SelectBox
    {
        return parent::addSelect(
            $name,
            $label,
            Countries::getNames($this->getTranslator()->getLocale())
        )
            ->setPrompt($this->getTranslator()->translate('helppcUtils.input.prompt.country'))
            ->setTranslator(null)
            ->setDefaultValue('CZ');
    }

    public function addIco(string $name, string $label, string $message = 'helppcUtils.input.validationError.identificationNumber'): TextInput
    {
        return parent::addText($name, $label)
            ->setRequired(false)
            ->addRule([$this->getValidator(), 'validateIco'], $message);
    }

    public function addPhone(string $name, string $label, string $message = 'helppcUtils.input.validationError.phoneNumber'): TextInput
    {
        return parent::addText($name, $label)
            ->setRequired(false);
    }

    public function addRc(string $name, string $label, string $message = 'helppcUtils.input.validationError.personalNumber'): TextInput
    {
        return parent::addText($name, $label)
            ->setRequired(false)
            ->addRule([$this->getValidator(), 'validateRc'], $message);
    }

    public function setBootstrapRenderer(): void
    {
        /** @var DefaultFormRenderer $renderer */
        $renderer = $this->getRenderer();
        $renderer->wrappers['controls']['container'] = null;
        $renderer->wrappers['pair']['container'] = 'div class=form-group';
        $renderer->wrappers['pair']['.error'] = 'has-error';
        $renderer->wrappers['control']['container'] = 'div class=col-sm-9';
        $renderer->wrappers['label']['container'] = 'div class="col-sm-3 control-label"';
        $renderer->wrappers['control']['description'] = 'span class=help-block';
        $renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';
        $this->getElementPrototype()->class('form-horizontal');
        $this->onRender[] = function (Form $form): void {
            foreach ($this->getControls() as $control) {
                $type = $control->getOption('type');
                if ($type === 'button') {
                    $control->getControlPrototype()->addClass(empty($usedPrimary) ? 'btn btn-primary' : 'btn btn-default');
                    $usedPrimary = true;
                } elseif (in_array($type, ['text', 'textarea', 'select'], true)) {
                    $control->getControlPrototype()->addClass('form-control form-control-sm');
                } elseif (in_array($type, ['checkbox', 'radio'], true)) {
                    $control->getSeparatorPrototype()->setName('div')->addClass($type);
                }
            }
        };
    }

}
