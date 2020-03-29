<?php

declare(strict_types=1);

namespace Sushi\Validator;

use Illuminate\Support\MessageBag;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory as ValidationFactory;
use Illuminate\Validation\ValidationException;
use Sushi\ValidatorInterface;
use Sushi\ValueObject;

class IlluminateValidationValidator implements ValidatorInterface
{
    public const LOCALE = 'en';

    public const INSTANCEOF_KEYWORD = 'instance_of';

    public const NO_DATA = 'No data';

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

        try{
            $this
                ->validatorFactory
                ->make($data, $rules)
                ->validate();
        } catch (ValidationException $exception) {
            /** @var MessageBag $bag */
            if (($bag = $exception->validator->errors()) instanceof MessageBag) {
                $errors = $bag->toArray();
            } else {
                $errors = [self::NO_DATA];
            }

            throw Exception\ValidationException::errors(class_basename($valueObject), $errors);
        }
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
