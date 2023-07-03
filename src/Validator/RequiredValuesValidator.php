<?php

namespace Mashbo\CoreImport\Validator;

class RequiredValuesValidator implements Validator
{
    /** @param array<array-key, string|int> $requiredValues */
    public function __construct(protected array $requiredValues)
    {
    }

    public function validate(array &$errors, int $row, ?string $column, mixed $value): void
    {
        if (!is_array($value)) {
            throw new \LogicException('Unexpected type passed to UniqueValidator');
        }

        foreach ($this->requiredValues as $item) {
            if (!in_array($item, $value)) {
                $errors[] = new RequiredValuesError($item, $row, $column);
            }
        }
    }
}