<?php

namespace Mashbo\CoreImport\Validator;

class UniqueValidator implements Validator
{
    /** @param array<array-key, ValidationError> $errors */
    public function validate(array &$errors, int $row, ?string $column, mixed $value): void
    {
        if (!is_iterable($value)) {
            throw new \LogicException('Unexpected type passed to UniqueValidator');
        }

        $duplicateCount = [];
        $duplicates = [];

        /**
         * Assume this is validating unique list of strings/ints
         * @var string|int $item
         */
        foreach ($value as $item) {
            if (!isset($duplicateCount[$item])) {
                $duplicateCount[$item] = 1;
            } else {
                $duplicateCount[$item]++;
            }

            if ($duplicateCount[$item] === 2) {
                $duplicates[] = $item;
            }
        }

        foreach ($duplicates as $duplicate) {
            $errors[] = new UniqueError(sprintf('%s is a duplicate', $duplicate), $row, (string)$duplicate);
        }
    }
}