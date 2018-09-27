<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Sushi\Validator\IlluminateValidationValidator;
use Sushi\Validator\KeysValidator;
use Sushi\ValueObject;

class IlluminateValidationDBTest extends TestCase
{
    const VALIDATORS = [
        KeysValidator::class,
        IlluminateValidationValidator::class,
    ];

    public function testValidation()
    {
        $this->expectException(\RuntimeException::class);

        new class ([
            'id' => 10,
        ]) extends ValueObject {
            protected $validators = IlluminateValidationTest::VALIDATORS;
            protected $keys = [
                'id' => 'required|unique:sometable',
            ];
        };
    }
}
