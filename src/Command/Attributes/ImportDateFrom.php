<?php

declare(strict_types=1);

namespace Mashbo\CoreImport\Command\Attributes;

#[\Attribute]
class ImportDateFrom extends ImportFrom
{
    public function __construct(string $header, protected string $format = 'd/m/Y', protected bool $nullable = false)
    {
        parent::__construct($header);
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }
}
