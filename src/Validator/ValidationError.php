<?php

namespace Mashbo\CoreImport\Validator;

class ValidationError
{
    public function __construct(
        protected string $message,
        protected ?int $row,
        protected ?string $column)
    {
        $this->row = $row;
        $this->message = $message;
        $this->column = $column;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getRow(): ?int
    {
        return $this->row;
    }

    public function getColumn(): ?string
    {
        return $this->column;
    }
}