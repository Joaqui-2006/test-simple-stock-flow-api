<?php

declare(strict_types=1);

namespace Tests\Domain;

use App\Domain\Exception\InsufficientStockException;
use App\Domain\Exception\InvalidPriceException;
use App\Domain\Model\Product;
use App\Domain\ValueObject\CategoryId;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\ProductId;
use App\Domain\ValueObject\Quantity;
use PHPUnit\Framework\TestCase;

final class ProductTest extends TestCase
{
    public function testCannotCreateProductWithNegativePrice(): void
    {
        $this->expectException(InvalidPriceException::class);
        Money::strictlyPositive(-10.0, 'COP');
    }

    public function testCannotCreateProductWithZeroPrice(): void
    {
        $this->expectException(InvalidPriceException::class);
        Money::strictlyPositive(0, 'COP');
    }

    public function testDeductStockSucceeds(): void
    {
        $product = new Product(
            new ProductId('p-1'),
            'CafÃ©',
            Money::fromNumber(1000, 'COP'),
            10,
            new CategoryId('c-1')
        );

        $product->deductStock(new Quantity(4));
        $this->assertSame(6, $product->getStock());
    }

    public function testDeductStockThrowsWhenInsufficient(): void
    {
        $product = new Product(
            new ProductId('p-1'),
            'CafÃ©',
            Money::fromNumber(1000, 'COP'),
            3,
            new CategoryId('c-1')
        );

        $this->expectException(InsufficientStockException::class);
        $product->deductStock(new Quantity(5));
    }
}