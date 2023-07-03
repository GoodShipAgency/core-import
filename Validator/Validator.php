<?php

namespace Mashbo\CoreImport\Validator;

interface Validator
{
    /** @param array<array-key, ValidationError> $errors */
    public function validate(array &$errors, int $row, ?string $column, mixed $value): void;
}