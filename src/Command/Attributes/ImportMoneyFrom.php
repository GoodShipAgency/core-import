<?php

namespace Mashbo\CoreImport\Command\Attributes;

#[\Attribute]
class ImportMoneyFrom extends ImportFrom
{
    public function __construct(string $header, protected string $currency = 'GBP', protected bool $nullable = false)
    {
        parent::__construct($header);
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }
}