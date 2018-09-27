<?php

declare(strict_types=1);

namespace Sushi\Validator;

use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory as ValidationFactory;
use Sushi\ValidatorInterface;
use Sushi\ValueObject;

class IlluminateValidationValidator implements ValidatorInterface
{
    const LOCALE = 'en';

    private $validatorFactory;

    public function __construct()
    {
        $this->validatorFactory = $this->instantiateValidationFactory();
    }

    public function validate(ValueObject $valueObject): void
    {
        $data = $valueObject->toArray();
        $rules = $valueObject->getKeysWithDefinitions();

        $this
            ->validatorFactory
            ->make($data, $rules)
            ->validate();
    }

    private function instantiateValidationFactory(): ValidationFactory
    {
        $loader = new ArrayLoader();
        $translator = new Translator($loader, self::LOCALE);
        $translator->setFallback(self::LOCALE);
        $validationFactory = new ValidationFactory($translator, null);

        return $validationFactory;
    }
}
