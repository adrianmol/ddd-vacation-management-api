<?php

declare(strict_types=1);

namespace VM\Infrastructure\Validation;

use VM\Infrastructure\Core\Exception\InvalidArgumentException;

class Validator
{
    /**
     * @throws \JsonException
     * @throws InvalidArgumentException
     */
    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            $fieldRules = is_string($fieldRules)
                ? explode('|', $fieldRules)
                : (array) $fieldRules;

            foreach ($fieldRules as $rule) {
                [$name, $param] = array_pad(explode(':', $rule, 2), 2, null);
                $failed = match ($name) {
                    'required' => (null === $value || '' === $value),
                    'int' => null !== $value && false === filter_var($value, FILTER_VALIDATE_INT),
                    'string' => null !== $value && !is_string($value),
                    'date' => null !== $value && !static::isValidDate($value),
                    'date_format' => null !== $value && !static::isValidDate($value, $param),
                    'min' => is_string($value) ? (strlen($value) < (int) $param)
                        : (is_numeric($value) && $value < (int) $param),
                    'max' => is_string($value) ? (strlen($value) > (int) $param)
                        : (is_numeric($value) && $value > (int) $param),
                    'between' => is_numeric($value) && ($value < (int) explode(',', $param)[0] || $value > (int) explode(',', $param)[1]),
                    default => false,
                };

                if ($failed) {
                    $errors[$field][] = static::message($field, $name, $param);
                }
            }
        }

        if ($errors) {
            throw new InvalidArgumentException($errors);
        }

        return $data;
    }

    private static function message(string $field, string $rule, ?string $param): string
    {
        return match ($rule) {
            'required' => "The $field field is required.",
            'int' => "The $field field must be an integer.",
            'string' => "The $field field must be a string.",
            'min' => "The $field field must be at least $param.",
            'max' => "The $field field must not exceed $param.",
            'between' => "The $field field must be between $param.",
            default => "The $field field is invalid.",
        };
    }

    private static function isValidDate(string $date, string $format = 'Y-m-d'): bool
    {
        $d = \DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) === $date;
    }
}
