<?php declare(strict_types=1);

namespace HelpPC\NetteUtils\Utils;

use Nette\Forms\Controls;
use Nette\Forms\Controls\TextInput;
use Nette\Forms\Form;
use Nette\Forms\IControl;
use Nette\InvalidArgumentException;
use Nette\Utils\RegexpException;
use Nette\Utils\Strings;

class FormValidator implements IFormValidator
{

    private Validator $validator;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function validateIco(IControl $control): bool
    {
        return $this->validator->validateIc($control->getValue());
    }

    public function validateUrl(IControl $control): bool
    {
        return $this->validator->validateUrl($control->getValue());
    }

    public function validateRc(IControl $control): bool
    {
        return $this->validator->validateRC($control->getValue());
    }

    public function validateEmail(IControl $control, bool $checkMX = false): bool
    {
        if (!$control instanceof TextInput) {
            throw new InvalidArgumentException(sprintf('This validator could be used only on text field. You used it on: "%s"', get_class($control)));
        }
        return $this->validator->validateEmail($control->getValue(), $checkMX);
    }

    public function validateByRegexp(IControl $control, string $pattern = '/^.{5}-.{2}-.{3}$/'): bool
    {
        if (!$control instanceof TextInput) {
            throw new InvalidArgumentException(sprintf('This validator could be used only on text field. You used it on: "%s"', get_class($control)));
        }
        try {
            return !empty(Strings::match($control->getValue(), $pattern));
        } catch (RegexpException $exception) {
            return false;
        }
    }

    public function validateVatNumber(IControl $control, bool $params = false): bool
    {
        if (!$control instanceof TextInput) {
            throw new InvalidArgumentException(sprintf('This validator could be used only on text field. You used it on: "%s"', get_class($control)));
        }
        return $this->validator->validateVatNumber($control->getValue(), $params);
    }

    public function setValidatorMessages(): void
    {
        $validatorMessages = &\Nette\Forms\Validator::$messages;

        $validatorMessages[Controls\CsrfProtection::PROTECTION] = 'helppcUtils.formValidation.csrfProtection';
        $validatorMessages[Form::EQUAL] = 'helppcUtils.formValidation.equal';
        $validatorMessages[Form::NOT_EQUAL] = 'helppcUtils.formValidation.notEqual';
        $validatorMessages[Form::FILLED] = 'helppcUtils.formValidation.filled';
        $validatorMessages[Form::BLANK] = 'helppcUtils.formValidation.blank';
        $validatorMessages[Form::MIN_LENGTH] = 'helppcUtils.formValidation.minLength';
        $validatorMessages[Form::MAX_LENGTH] = 'helppcUtils.formValidation.maxLength';
        $validatorMessages[Form::LENGTH] = 'helppcUtils.formValidation.length';
        $validatorMessages[Form::EMAIL] = 'helppcUtils.formValidation.email';
        $validatorMessages[Form::URL] = 'helppcUtils.formValidation.url';
        $validatorMessages[Form::INTEGER] = 'helppcUtils.formValidation.integer';
        $validatorMessages[Form::FLOAT] = 'helppcUtils.formValidation.float';
        $validatorMessages[Form::MIN] = 'helppcUtils.formValidation.min';
        $validatorMessages[Form::MAX] = 'helppcUtils.formValidation.max';
        $validatorMessages[Form::RANGE] = 'helppcUtils.formValidation.range';
        $validatorMessages[Form::MAX_FILE_SIZE] = 'helppcUtils.formValidation.maxFileSize';
        $validatorMessages[Form::MAX_POST_SIZE] = 'helppcUtils.formValidation.maxPostSize';
        $validatorMessages[Form::MIME_TYPE] = 'helppcUtils.formValidation.mimeType';
        $validatorMessages[Form::IMAGE] = 'helppcUtils.formValidation.image';
        $validatorMessages[Controls\SelectBox::VALID] = 'helppcUtils.formValidation.selectValid';
        $validatorMessages[Controls\UploadControl::VALID] = 'helppcUtils.formValidation.uploadValid';
    }

}
