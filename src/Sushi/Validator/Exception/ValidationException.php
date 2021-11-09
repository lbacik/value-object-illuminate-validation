<?php

declare(strict_types=1);

namespace Sushi\Validator\Exception;

use RuntimeException;

class ValidationException extends RuntimeException
{
    public static function errors(string $class, array $errors): self
    {
        $items = [];
        foreach ($errors as $key => $err) {
            $items[] = "{$key} - " . implode('/', $err);
        }
        return new static("Validation problems in {$class}: " . implode(',', $items));
    }
}
