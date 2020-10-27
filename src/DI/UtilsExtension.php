<?php declare(strict_types=1);

namespace HelpPC\NetteUtils\DI;

use Contributte\Translation\DI\TranslationProviderInterface;
use HelpPC\NetteUtils\UI\FormFactory;
use HelpPC\NetteUtils\UI\IFormFactory;
use HelpPC\NetteUtils\Utils\FormValidator;
use HelpPC\NetteUtils\Utils\Tempnam;
use HelpPC\NetteUtils\Utils\Validator;
use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

class UtilsExtension extends CompilerExtension implements TranslationProviderInterface
{
    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'dateFormat' => Expect::string('j. n. Y')->dynamic(),
            'dateTimeFormat' => Expect::string('j. n. Y H:i')->dynamic(),
        ])->castTo('array');
    }

    public function getTranslationResources(): array
    {
        return [
            __DIR__ . '/../lang',
        ];
    }


    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();

        $validator = $builder->addDefinition($this->prefix('validator'))
            ->setFactory(Validator::class);

        $formValidator = $builder->addDefinition($this->prefix('formValidator'))
            ->setFactory(FormValidator::class, [$validator]);
        $builder->addDefinition($this->prefix('tempnam'))
            ->setFactory(Tempnam::class, ['%tempDir%/tempFiles']);

        $builder->addDefinition($this->prefix('formFactory'))
            ->setFactory(FormFactory::class, [$formValidator]);
    }

    public function afterCompile(ClassType $class): void
    {
        parent::afterCompile($class);

        $config = $this->getConfig();

        $initialize = $class->methods['initialize'];
        $initialize->addBody('RadekDostal\NetteComponents\DateTimePicker\TbDatePicker::register(?);', [$config['dateFormat']]);
        $initialize->addBody('RadekDostal\NetteComponents\DateTimePicker\TbDateTimePicker::register(?);', [$config['dateTimeFormat']]);
    }
}