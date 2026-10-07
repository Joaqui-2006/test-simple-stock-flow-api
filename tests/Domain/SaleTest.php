<?php

declare(strict_types=1);

namespace Tests\Domain;

use App\Domain\Exception\EmptySaleException;
use App\Domain\Exception\RepeatedProductException;
use App\Domain\Model\Sale;
use App\Domain\Model\SaleItem;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\ProductId;
use App\Domain\ValueObject\Quantity;
use App\Domain\ValueObject\SaleId;
use App\Domain\ValueObject\UserId;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class SaleTest extends TestCase
{
    public function testCannotCreateSaleWithEmptyItems(): void
    {
        $this->expectException(EmptySaleException::class);

        new Sale(
            new SaleId('sale-1'),
            new UserId('user-1'),
            'seller_demo',
            new DateTimeImmutable(),
            []
        );
    }

    public function testCannotCreateSaleWithRepeatedProducts(): void
    {
        $this->expectException(RepeatedProductException::class);

        $prodId = new ProductId('prod-1');
        $item1 = new SaleItem($prodId, 'Café', Money::fromNumber(1000, 'COP'), new Quantity(2));
        $item2 = new SaleItem($prodId, 'Café', Money::fromNumber(1000, 'COP'), new Quantity(3));

        new Sale(
            new SaleId('sale-1'),
            new UserId('user-1'),
            'seller_demo',
            new DateTimeImmutable(),
            [$item1, $item2]
        );
    }

    public function testTotalIsDerivedAtRuntimeFromItems(): void
    {
        $item1 = new SaleItem(
            new ProductId('prod-1'),
            'Café Especial 500g',
            Money::fromNumber(24000, 'COP'),
            new Quantity(2)
        );
        $item2 = new SaleItem(
            new ProductId('prod-2'),
            'Té Verde',
            Money::fromNumber(12500, 'COP'),
            new Quantity(3)
        );

        $sale = new Sale(
            new SaleId('sale-1'),
            new UserId('user-1'),
            'seller_demo',
            new DateTimeImmutable(),
            [$item1, $item2]
        );

        // 24000 * 2 + 12500 * 3 = 48000 + 37500 = 85500
        $this->assertSame(85500.0, $sale->getTotal()->toFloat());
        $this->assertSame('COP', $sale->getTotal()->getCurrency());
    }
}
