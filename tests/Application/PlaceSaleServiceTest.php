<?php

declare(strict_types=1);

namespace Tests\Application;

use App\Application\Model\PageRequest;
use App\Application\Ports\Inbound\PagedResult;
use App\Application\Ports\Inbound\PlaceSaleCommand;
use App\Application\Ports\Outbound\Clock;
use App\Application\Ports\Outbound\ProductRepository;
use App\Application\Ports\Outbound\SaleRepository;
use App\Application\Ports\Outbound\UnitOfWork;
use App\Application\UseCase\PlaceSaleService;
use App\Domain\Exception\InsufficientStockException;
use App\Domain\Exception\ProductNotFoundException;
use App\Domain\Model\Product;
use App\Domain\Model\Sale;
use App\Domain\ValueObject\CategoryId;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\ProductId;
use App\Domain\ValueObject\Quantity;
use App\Domain\ValueObject\SaleId;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class PlaceSaleServiceTest extends TestCase
{
    private ProductRepository $productRepo;
    private SaleRepository $saleRepo;
    private UnitOfWork $unitOfWork;
    private Clock $clock;
    private PlaceSaleService $service;

    /** @var array<string, Product> */
    private array $products = [];
    /** @var array<string, Sale> */
    private array $sales = [];

    protected function setUp(): void
    {
        $this->products = [];
        $this->sales = [];

        // In-memory fake ProductRepository
        $this->productRepo = new class($this->products) implements ProductRepository {
            public function __construct(private array &$storage) {}
            public function findById(ProductId $id): ?Product {
                return $this->storage[$id->toString()] ?? null;
            }
            public function listActive(?string $search, ?CategoryId $categoryId, PageRequest $pageRequest): PagedResult {
                return new PagedResult(array_values($this->storage), count($this->storage), 1, 20);
            }
            public function save(Product $product): void {
                $this->storage[$product->getId()->toString()] = $product;
            }
            public function nextIdentity(): ProductId {
                return new ProductId('prod-' . uniqid());
            }
        };

        // In-memory fake SaleRepository
        $this->saleRepo = new class($this->sales) implements SaleRepository {
            public function __construct(private array &$storage) {}
            public function findById(SaleId $id): ?Sale {
                return $this->storage[$id->toString()] ?? null;
            }
            public function listSales(?DateTimeImmutable $from, ?DateTimeImmutable $to, PageRequest $pageRequest): PagedResult {
                return new PagedResult(array_values($this->storage), count($this->storage), 1, 20);
            }
            public function save(Sale $sale): void {
                $this->storage[$sale->getId()->toString()] = $sale;
            }
            public function nextIdentity(): SaleId {
                return new SaleId('sale-100');
            }
        };

        // In-memory fake UnitOfWork (direct execution without DB)
        $this->unitOfWork = new class implements UnitOfWork {
            public function run(callable $operation): mixed {
                return $operation();
            }
        };

        // Fake Clock
        $this->clock = new class implements Clock {
            public function now(): DateTimeImmutable {
                return new DateTimeImmutable('2026-10-06T12:00:00+00:00');
            }
        };

        $this->service = new PlaceSaleService(
            $this->productRepo,
            $this->saleRepo,
            $this->unitOfWork,
            $this->clock
        );
    }

    public function testPlaceSaleDecrementsStockAndSavesSale(): void
    {
        $prodId = new ProductId('prod-1');
        $product = new Product(
            $prodId,
            'Café Especial Huila',
            Money::fromNumber(24000, 'COP'),
            10,
            new CategoryId('cat-1')
        );
        $this->productRepo->save($product);

        $cmd = new PlaceSaleCommand(
            'seller-uuid-1',
            'seller_demo',
            [['productId' => 'prod-1', 'quantity' => 2]]
        );

        $result = $this->service->execute($cmd);

        $this->assertSame('sale-100', $result->id);
        $this->assertSame(48000.0, $result->total);
        $this->assertSame('COP', $result->currency);
        $this->assertCount(1, $result->items);
        $this->assertSame('Café Especial Huila', $result->items[0]->productName);

        // Verify stock was decremented in memory repository
        $updatedProd = $this->productRepo->findById($prodId);
        $this->assertNotNull($updatedProd);
        $this->assertSame(8, $updatedProd->getStock());

        // Verify sale was saved
        $this->assertCount(1, $this->sales);
    }

    public function testPlaceSaleThrowsWhenStockInsufficient(): void
    {
        $prodId = new ProductId('prod-1');
        $product = new Product(
            $prodId,
            'Té Verde Orgánico',
            Money::fromNumber(12500, 'COP'),
            3,
            new CategoryId('cat-1')
        );
        $this->productRepo->save($product);

        $cmd = new PlaceSaleCommand(
            'seller-uuid-1',
            'seller_demo',
            [['productId' => 'prod-1', 'quantity' => 5]]
        );

        $this->expectException(InsufficientStockException::class);
        $this->service->execute($cmd);
    }

    public function testPlaceSaleThrowsWhenProductNotFound(): void
    {
        $cmd = new PlaceSaleCommand(
            'seller-uuid-1',
            'seller_demo',
            [['productId' => 'non-existent-product', 'quantity' => 1]]
        );

        $this->expectException(ProductNotFoundException::class);
        $this->service->execute($cmd);
    }
}
