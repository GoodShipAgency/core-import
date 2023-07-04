<?php

declare(strict_types=1);

namespace Mashbo\CoreImport\Command;

use Mashbo\CoreImport\Command\Attributes\ImportBoolFrom;
use Mashbo\CoreImport\Command\Attributes\ImportDateFrom;
use Mashbo\CoreImport\Command\Attributes\ImportFrom;
use Mashbo\CoreImport\Command\Attributes\ImportMoneyFrom;
use Money\Currency;
use Money\Money;

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
            'int' => intval($value),
            'DateTimeImmutable' => static::createDateTimeImmutable($attribute, $value),
            'bool' => static::createBool($property, $attribute, $value),
            Money::class => static::createMoney($property, $attribute, $value),
            default => throw new \LogicException(sprintf('Unknown property type "%s"', $propertyName))
        };
    }

    private static function createDateTimeImmutable(ImportFrom $attribute, mixed $value): ?\DateTimeImmutable
    {
        if ($attribute instanceof ImportDateFrom) {
            $format = $attribute->getFormat();

            if ($value === "" || $value === null) {
                if ($attribute->isNullable()) {
                    return null;
                }
            }

            $dateTime = \DateTimeImmutable::createFromFormat($format, (string)$value);

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

        throw new \LogicException('Unable to handle bool value ' . (string)$value);
    }

    private static function createMoney(\ReflectionProperty $property, ImportFrom $attribute, mixed $value): ?Money
    {
        if ($attribute instanceof ImportMoneyFrom) {
            if ($value === "" || $value === null) {
                if ($attribute->isNullable()) {
                    return null;
                }
            }

            $currency = $attribute->getCurrency();

            return new Money($value, new Currency($currency));
        }

        return Money::GBP($value);
    }

}
