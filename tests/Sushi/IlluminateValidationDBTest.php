<?php

declare(strict_types=1);

namespace Tests\Sushi;

use PHPUnit\Framework\TestCase;
use Sushi\Validator\IlluminateValidationValidator;
use Sushi\Validator\KeysValidator;
use Sushi\ValueObject;

class IlluminateValidationDBTest extends TestCase
{
    public const VALIDATORS = [
        KeysValidator::class,
        IlluminateValidationValidator::class,
    ];

    public function testValidation()
    {
        $this->expectException(\RuntimeException::class);

        new class ([
            'id' => 10,
        ]) extends ValueObject {
            protected array $validators = IlluminateValidationTest::VALIDATORS;
            protected array $keys = [
                'id' => 'required|unique:sometable',
            ];
        };
    }
}
