<?php

declare(strict_types=1);

namespace App\CareerConnect\Application\Common\ImportCommandConverter\Attributes;

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
