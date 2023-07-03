<?php

declare(strict_types=1);

namespace App\CareerConnect\Application\Common\ImportCommandConverter;

use App\CareerConnect\Application\Common\ImportCommandConverter\Attributes\ImportBoolFrom;
use App\CareerConnect\Application\Common\ImportCommandConverter\Attributes\ImportDateFrom;
use App\CareerConnect\Application\Common\ImportCommandConverter\Attributes\ImportFrom;

/**
 * @psalm-consistent-constructor
 */
abstract class AbstractImportCommand
{
    public static function fromArray(array $arr): static
    {
        $self = new static();

        $reflectionClass = new \ReflectionClass(static::class);

        foreach ($reflectionClass->getProperties() as $property) {
            $attributes = $property->getAttributes();
            foreach ($attributes as $attribute) {
                $attributeInstance = $attribute->newInstance();
                if (!$attributeInstance instanceof ImportFrom) {
                    break;
                }

                // Null check to allow coalescing multiple ImportFrom
                if (!isset($self->{$property->name}) || $self->{$property->name} === null) {
                    $self->{$property->name} = static::createParameterValue(
                        $property,
                        $attributeInstance,
                        $arr[$attributeInstance->getHeader()] ?? null
                    );
                }
            }
        }

        return $self;
    }

    private static function createParameterValue(\ReflectionProperty $property, ImportFrom $attribute, mixed $value): mixed
    {
        /** @var ?\ReflectionNamedType $propertyType */
        $propertyType = $property->getType();

        if ($value === null) {
            if ($propertyType?->allowsNull()) {
                return null;
            } else {
                throw new \LogicException('Property does not allow null');
            }
        }

        $propertyName = $propertyType?->getName() ?? 'null';

        return match ($propertyName) {
            'string' => $value,
            'DateTimeImmutable' => static::createDateTimeImmutable($attribute, (string) $value),
            'bool' => static::createBool($property, $attribute, $value),
            default => throw new \LogicException(sprintf('Unknown property type "%s"', $propertyName))
        };
    }

    private static function createDateTimeImmutable(ImportFrom $attribute, string $value): \DateTimeImmutable
    {
        if ($attribute instanceof ImportDateFrom) {
            $format = $attribute->getFormat();

            $dateTime = \DateTimeImmutable::createFromFormat($format, $value);

            if ($dateTime === false) {
                throw new \LogicException(sprintf('DateTimeImmutable "%s" did not match format "%s"', $value, $format));
            }

            return $dateTime;
        }

        return new \DateTimeImmutable($value);
    }

    private static function createBool(\ReflectionProperty $property, ImportFrom $attribute, mixed $value): ?bool
    {
        if (is_string($value)) {
            $value = strtolower($value);
        }

        if (!$attribute instanceof ImportBoolFrom) {
            $attribute = new ImportBoolFrom($attribute->getHeader());
        }

        if (in_array($value, $attribute->getTrueValues(), true)) {
            return true;
        }

        if (in_array($value, $attribute->getFalseValues(), true)) {
            return false;
        }

        if (empty($value)) {
            if ($property->getType()?->allowsNull()) {
                return null;
            } else {
                return false;
            }
        }

        throw new \LogicException('Unable to handle bool value '.(string) $value);
    }
}
