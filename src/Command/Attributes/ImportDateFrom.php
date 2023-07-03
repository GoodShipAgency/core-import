<?php

declare(strict_types=1);

namespace Mashbo\CoreImport\Command\Attributes;

#[\Attribute]
class ImportDateFrom extends ImportFrom
{
    public function __construct(string $header, protected string $format = 'd/m/Y')
    {
        parent::__construct($header);
    }

    public function getFormat(): string
    {
        return $this->format;
    }
}
