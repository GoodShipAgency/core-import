<?php

namespace Mashbo\CoreImport\Validator;

class RequiredValuesError extends ValidationError
{
    public function __construct(mixed $value, ?int $row, ?string $column)
    {
        $this->message = sprintf('%s is a required value', (string)$value);
        $this->row = $row;
        $this->column = $column;
    }
}