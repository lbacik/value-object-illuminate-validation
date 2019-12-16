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

    const INSTANCEOF_KEYWORD = 'instance_of';

    private $validatorFactory;

    public function __construct()
    {
        $this->validatorFactory = $this->instantiateValidationFactory();
        $this->instanceOfExtension();
    }

    public function validate(ValueObject $valueObject): void
    {
        $rules = $valueObject->getKeysWithDefinitions();
        $data = $this->getValues($valueObject, array_keys($rules));

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

    private function instanceOfExtension(): void
    {
        $this
            ->validatorFactory
            ->extend(self::INSTANCEOF_KEYWORD, function ($attribute, $value, $classNameArray) {
                foreach($classNameArray as $className) {
                    if (! $value instanceof $className) {
                        return false;
                    }
                }
                return true;
            });
    }

    private function getValues(ValueObject $valueObject, array $keys): array
    {
        $data = [];
        foreach ($keys as $key) {
            $data[$key] = $valueObject[$key];
        }
        return $data;
    }
}
