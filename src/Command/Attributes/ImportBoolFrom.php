<?php

declare(strict_types=1);

namespace Mashbo\CoreImport\Command\Attributes;

#[\Attribute]
class ImportBoolFrom extends ImportFrom
{
    public const DEFAULT_TRUE_VALUES = ['t', '1', 'y', 1, 'yes'];
    public const DEFAULT_FALSE_VALUES = ['f', '0', 'n', 0, 'no'];

    public function __construct(
        string $header,
        protected array $trueValues = self::DEFAULT_TRUE_VALUES,
        protected array $falseValues = self::DEFAULT_FALSE_VALUES
    ) {
        parent::__construct($header);
    }

    public function getTrueValues(): array
    {
        return $this->trueValues;
    }

    public function getFalseValues(): array
    {
        return $this->falseValues;
    }
}
