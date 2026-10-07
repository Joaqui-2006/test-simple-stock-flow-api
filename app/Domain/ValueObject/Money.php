<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\InvalidPriceException;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

final class Money
{
    private BigDecimal $amount;
    private string $currency;

    private function __construct(BigDecimal $amount, string $currency = 'COP')
    {
        $this->amount = $amount->toScale(2, RoundingMode::HALF_UP);
        $this->currency = strtoupper(trim($currency));
    }

    public static function fromNumber(int|float|string $value, string $currency = 'COP'): self
    {
        $decimal = BigDecimal::of((string) $value);
        if ($decimal->isNegative()) {
            throw new InvalidPriceException('El importe monetario no puede ser negativo');
        }
        return new self($decimal, $currency);
    }

    public static function strictlyPositive(int|float|string $value, string $currency = 'COP'): self
    {
        $money = self::fromNumber($value, $currency);
        if ($money->amount->isZero()) {
            throw new InvalidPriceException('El precio debe ser estrictamente mayor que cero');
        }
        return $money;
    }

    public static function zero(string $currency = 'COP'): self
    {
        return new self(BigDecimal::zero(), $currency);
    }

    public function add(self $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount->plus($other->amount), $this->currency);
    }

    public function multiply(int $multiplier): self
    {
        return new self($this->amount->multipliedBy($multiplier), $this->currency);
    }

    public function getAmount(): BigDecimal
    {
        return $this->amount;
    }

    public function toFloat(): float
    {
        return $this->amount->toFloat();
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function equals(self $other): bool
    {
        return $this->currency === $other->currency && $this->amount->isEqualTo($other->amount);
    }

    private function assertSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidPriceException(
                sprintf('No se pueden operar importes en distintas monedas: %s y %s', $this->currency, $other->currency)
            );
        }
    }
}