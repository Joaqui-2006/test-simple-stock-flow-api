<?php

declare(strict_types=1);

namespace Tests\Domain;

use App\Domain\Exception\InvalidQuantityException;
use App\Domain\ValueObject\Quantity;
use PHPUnit\Framework\TestCase;

final class QuantityTest extends TestCase
{
    public function testPositiveQuantityIsValid(): void
    {
        $q = new Quantity(5);
        $this->assertSame(5, $q->toInt());
    }

    public function testZeroQuantityThrowsException(): void
    {
        $this->expectException(InvalidQuantityException::class);
        new Quantity(0);
    }

    public function testNegativeQuantityThrowsException(): void
    {
        $this->expectException(InvalidQuantityException::class);
        new Quantity(-3);
    }
}
