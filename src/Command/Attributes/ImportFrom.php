<?php

declare(strict_types=1);

namespace Mashbo\CoreImport\Command\Attributes;

#[\Attribute]
class ImportFrom
{
    public function __construct(protected string $header)
    {
    }

    public function getHeader(): string
    {
        return $this->header;
    }
}
