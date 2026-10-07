<?php

declare(strict_types=1);

namespace App\Application\Model;

use DateTimeImmutable;
use InvalidArgumentException;

final class DateRange
{
    public function __construct(
        private readonly DateTimeImmutable $from,
        private readonly DateTimeImmutable $to
    ) {
        if ($from > $to) {
            throw new InvalidArgumentException('La fecha de inicio no puede ser posterior a la fecha final');
        }
    }

    public function getFrom(): DateTimeImmutable
    {
        return $this->from;
    }

    public function getTo(): DateTimeImmutable
    {
        return $this->to;
    }
}