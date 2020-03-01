<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Sushi\Validator\Exception\ValidationException;
use Sushi\Validator\IlluminateValidationValidator;
use Sushi\ValueObject;
use Sushi\ValueObject\Fields;

class IlluminateValidationIsInstanceOfTest extends TestCase
{
    const VALIDATORS = [
        IlluminateValidationValidator::class,
    ];

    public function testValidation(): void
    {
        $example = new ValueObject([]);

        $vo = new class ([
            'name' => $example,
        ]) extends ValueObject {
            protected $validators = IlluminateValidationIsInstanceOfTest::VALIDATORS;
            protected $keys = [
                'name' => IlluminateValidationValidator::INSTANCEOF_KEYWORD . ':' . ValueObject::class,
            ];
        };

        $this->assertInstanceOf(ValueObject::class, $vo);
    }

    public function testValidationBaseClass(): void
    {
        $example = new ValueObject([]);

        $vo = new class ([
            'name' => $example,
        ]) extends ValueObject {
            protected $validators = IlluminateValidationIsInstanceOfTest::VALIDATORS;
            protected $keys = [
                'name' => IlluminateValidationValidator::INSTANCEOF_KEYWORD . ':' . Fields::class,
            ];
        };

        $this->assertInstanceOf(ValueObject::class, $vo);
    }

    public function testValidationInterface(): void
    {
        $example = new ValueObject([]);

        $vo = new class ([
            'name' => $example,
        ]) extends ValueObject {
            protected $validators = IlluminateValidationIsInstanceOfTest::VALIDATORS;
            protected $keys = [
                'name' => IlluminateValidationValidator::INSTANCEOF_KEYWORD . ':' . \ArrayAccess::class,
            ];
        };

        $this->assertInstanceOf(ValueObject::class, $vo);
    }

    public function testValidationMulti(): void
    {
        $example = new ValueObject([]);

        $vo = new class ([
            'name' => $example,
        ]) extends ValueObject {
            protected $validators = IlluminateValidationIsInstanceOfTest::VALIDATORS;
            protected $keys = [
                'name' => IlluminateValidationValidator::INSTANCEOF_KEYWORD
                    . ':'
                    . ValueObject::class
                    . ','
                    . \ArrayAccess::class,
            ];
        };

        $this->assertInstanceOf(ValueObject::class, $vo);
    }

    public function testValidationNotImplementedInterface(): void
    {
        $this->expectException(ValidationException::class);

        $example = new ValueObject([]);

        new class ([
            'name' => $example,
        ]) extends ValueObject {
            protected $validators = IlluminateValidationIsInstanceOfTest::VALIDATORS;
            protected $keys = [
                'name' => IlluminateValidationValidator::INSTANCEOF_KEYWORD . ':' . \Iterator::class,
            ];
        };
    }

    public function testValidationMultiWithNotImplementedInterface(): void
    {
        $this->expectException(ValidationException::class);

        $example = new ValueObject([]);

        new class ([
            'name' => $example,
        ]) extends ValueObject {
            protected $validators = IlluminateValidationIsInstanceOfTest::VALIDATORS;
            protected $keys = [
                'name' => IlluminateValidationValidator::INSTANCEOF_KEYWORD
                    . ':'
                    . \ArrayAccess::class
                    . ','
                    . \Iterator::class,
            ];
        };
    }

    public function testValidationNull(): void
    {
        $this->expectException(ValidationException::class);

        new class ([
            'name' => null,
        ]) extends ValueObject {
            protected $validators = IlluminateValidationIsInstanceOfTest::VALIDATORS;
            protected $keys = [
                'name' => IlluminateValidationValidator::INSTANCEOF_KEYWORD . ':' . ValueObject::class,
            ];
        };
    }

    public function testValidationNullable(): void
    {
        $vo = new class ([
            'name' => null,
        ]) extends ValueObject {
            protected $validators = IlluminateValidationIsInstanceOfTest::VALIDATORS;
            protected $keys = [
                'name' => 'nullable|'
                    . IlluminateValidationValidator::INSTANCEOF_KEYWORD
                    . ':'
                    . ValueObject::class,
            ];
        };

        $this->assertInstanceOf(ValueObject::class, $vo);
    }
}
