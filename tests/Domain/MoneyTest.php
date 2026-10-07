<?php

declare(strict_types=1);

namespace Tests\Domain;

use App\Domain\Exception\InvalidPriceException;
use App\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function testValidMoneyCreation(): void
    {
        $m = Money::fromNumber(15000.50, 'COP');
        $this->assertSame(15000.50, $m->toFloat());
        $this->assertSame('COP', $m->getCurrency());
    }

    public function testStrictlyPositiveRejectsZero(): void
    {
        $this->expectException(InvalidPriceException::class);
        Money::strictlyPositive(0, 'COP');
    }

    public function testStrictlyPositiveRejectsNegative(): void
    {
        $this->expectException(InvalidPriceException::class);
        Money::strictlyPositive(-500, 'COP');
    }

    public function testMultiplicationProducesExpectedResult(): void
    {
        $unitPrice = Money::fromNumber(12500, 'COP');
        $total = $unitPrice->multiply(3);
        $this->assertSame(37500.0, $total->toFloat());
        $this->assertSame('COP', $total->getCurrency());
    }

    public function testAdditionCombinesAmounts(): void
    {
        $m1 = Money::fromNumber(10000, 'COP');
        $m2 = Money::fromNumber(5500, 'COP');
        $sum = $m1->add($m2);
        $this->assertSame(15500.0, $sum->toFloat());
    }
}
