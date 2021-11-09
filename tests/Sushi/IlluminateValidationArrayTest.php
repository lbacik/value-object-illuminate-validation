<?php

declare(strict_types=1);

namespace Tests\Sushi;

use PHPUnit\Framework\TestCase;
use stdClass;
use Sushi\Validator\Exception\ValidationException;
use Sushi\Validator\IlluminateValidationValidator;
use Sushi\ValueObject;
use Tests\Sushi\Resources\Item;

class IlluminateValidationArrayTest extends TestCase
{
    public const VALIDATORS = [
        IlluminateValidationValidator::class,
    ];

    public function testNull(): void
    {
        $this->expectException(ValidationException::class);

        $this->createValueObject(null);
    }

    public function testArrayEmpty(): void
    {
        $vo = $this->createValueObject([]);

        $this->assertInstanceOf(ValueObject::class, $vo);
    }

    public function testArrayInt(): void
    {
        $this->expectException(ValidationException::class);

        $this->createValueObject([1, 2, 3]);
    }

    public function testArrayAssocStr(): void
    {
        $this->expectException(ValidationException::class);

        $this->createValueObject(['a' => '1', 'b' => '2', 'c' => '3']);
    }

    public function testArrayOfObjects(): void
    {
        $this->expectException(ValidationException::class);

        $this->createValueObject([new stdClass(), new stdClass()]);
    }

    public function testArrayOfItems(): void
    {
        $vo = $this->createValueObject([new Item()]);

        $this->assertInstanceOf(ValueObject::class, $vo);
    }

    public function testAssocArrayOfItems(): void
    {
        $vo = $this->createValueObject(['1' => new Item(), 'b' => new Item()]);

        $this->assertInstanceOf(ValueObject::class, $vo);
    }

    public function testArrayOfAnyAndItems(): void
    {
        $this->expectException(ValidationException::class);

        $this->createValueObject([new Item(), new stdClass()]);
    }

    public function testArrayNull(): void
    {
        $this->expectException(ValidationException::class);

        $this->createValueObject([null]);
    }

    private function createValueObject(?array $array): ValueObject
    {
        return new class ([
            'array' => $array,
        ]) extends ValueObject {
            protected $validators = IlluminateValidationArrayTest::VALIDATORS;
            protected $keys = [
                'array' => 'present|array',
                'array.*' => 'instance_of:' . Item::class,
            ];
        };
    }
}
