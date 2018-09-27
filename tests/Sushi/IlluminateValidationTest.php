<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Illuminate\Validation\ValidationException;
use Sushi\Validator\IlluminateValidationValidator;
use Sushi\Validator\KeysValidator;
use Sushi\ValueObject;
use Sushi\Validator\Exceptions\NotExistingKeyException as ValidatorNotExistingKeyException;

class IlluminateValidationTest extends TestCase
{
    const VALIDATORS = [
        KeysValidator::class,
        IlluminateValidationValidator::class,
    ];
    const KEYS = [];
    const EXAMPLE_DATA = [];

    public function testInstantiate(): void
    {
        $vo = $this->getValueObject([]);

        $this->assertInstanceOf(ValueObject::class, $vo);
    }

    public function testInstantiateWithNotExistingKey(): void
    {
        $this->expectException(ValidatorNotExistingKeyException::class);

        $this->getValueObject([
            'notExisting' => 'foo',
        ]);
    }

    public function testGetNotExistingKey(): void
    {
        $vo = $this->getValueObject([]);

        $this->assertSame(null, $vo['notExisting']);
    }

    public function testValidation(): void
    {
        $vo = new class ([
            'name' => 'Ala',
        ]) extends ValueObject {
            protected $validators = IlluminateValidationTest::VALIDATORS;
            protected $keys = [
                'name' => 'required|string|max:5',
            ];
        };

        $this->assertInstanceOf(ValueObject::class, $vo);
        $this->assertSame($vo['name'], 'Ala');
    }

    public function testValidationFailRequired(): void
    {
        $this->expectException(ValidationException::class);

        new class ([]) extends ValueObject {
            protected $validators = IlluminateValidationTest::VALIDATORS;
            protected $keys = [
                'name' => 'required|string|max:5',
            ];
        };
    }

    public function testValidationFailString(): void
    {
        $this->expectException(ValidationException::class);

        new class ([
            'name' => 0,
        ]) extends ValueObject {
            protected $validators = IlluminateValidationTest::VALIDATORS;
            protected $keys = [
                'name' => 'required|string|max:5',
            ];
        };
    }

    public function testValidationFailMax(): void
    {
        $this->expectException(ValidationException::class);

        new class ([
            'name' => '123456',
        ]) extends ValueObject {
            protected $validators = IlluminateValidationTest::VALIDATORS;
            protected $keys = [
                'name' => 'required|string|max:5',
            ];
        };
    }

    public function testValidationMulti(): void
    {
        $vo = new class ([
            'id' => 1,
            'name' => 'Ala',
            'desc' => 'Sample description',
        ]) extends ValueObject {
            protected $validators = IlluminateValidationTest::VALIDATORS;
            protected $keys = [
                'id' => 'required|integer|min:1',
                'name' => 'required|string|max:5',
                'desc' => 'nullable|string|max:100',
            ];
        };

        $this->assertInstanceOf(ValueObject::class, $vo);
        $this->assertSame($vo['name'], 'Ala');
        $this->assertSame($vo['id'], 1);
        $this->assertSame($vo['desc'], 'Sample description');
    }

    public function testValidationOptional(): void
    {
        $vo = new class ([]) extends ValueObject {
            protected $validators = IlluminateValidationTest::VALIDATORS;
            protected $keys = [
                'desc' => 'nullable|string|max:100',
            ];
        };

        $this->assertInstanceOf(ValueObject::class, $vo);
        $this->assertSame($vo['desc'], null);
    }

    public function testValidationNull(): void
    {
        $vo = new class ([
            'desc' => null,
        ]) extends ValueObject {
            protected $validators = IlluminateValidationTest::VALIDATORS;
            protected $keys = [
                'desc' => 'nullable|string|max:100',
            ];
        };

        $this->assertInstanceOf(ValueObject::class, $vo);
        $this->assertSame($vo['desc'], null);
    }

    public function testValidationMultiCast(): void
    {
        $vo = new class ([
            'id' => '1',
        ]) extends ValueObject {
            protected $validators = IlluminateValidationTest::VALIDATORS;
            protected $keys = [
                'id' => 'required|integer|min:1',
            ];
        };

        $this->assertInstanceOf(ValueObject::class, $vo);
        $this->assertSame($vo['id'], '1');
    }

    public function testValidationMin(): void
    {
        $this->expectException(ValidationException::class);

        new class ([
            'id' => 0,
        ]) extends ValueObject {
            protected $validators = IlluminateValidationTest::VALIDATORS;
            protected $keys = [
                'id' => 'required|integer|min:1',
            ];
        };
    }

    private function getValueObject(array $values): ValueObject
    {
        return new class ($values) extends ValueObject {
            protected $validators = IlluminateValidationTest::VALIDATORS;
            protected $keys = IlluminateValidationTest::KEYS;
        };
    }
}
