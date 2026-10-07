<?php

declare(strict_types=1);

namespace App\Bootstrap;

use App\Application\Ports\Inbound\Authenticate;
use App\Application\Ports\Inbound\GetSales;
use App\Application\Ports\Inbound\GetSalesReport;
use App\Application\Ports\Inbound\ManageProducts;
use App\Application\Ports\Inbound\PlaceSale;
use App\Application\Ports\Outbound\CategoryRepository;
use App\Application\Ports\Outbound\Clock;
use App\Application\Ports\Outbound\FileStorage;
use App\Application\Ports\Outbound\PasswordHasher;
use App\Application\Ports\Outbound\ProductRepository;
use App\Application\Ports\Outbound\SaleRepository;
use App\Application\Ports\Outbound\SalesReportQuery;
use App\Application\Ports\Outbound\TokenGenerator;
use App\Application\Ports\Outbound\UnitOfWork;
use App\Application\Ports\Outbound\UserRepository;
use App\Application\UseCase\AuthenticationService;
use App\Application\UseCase\GetSalesService;
use App\Application\UseCase\PlaceSaleService;
use App\Application\UseCase\ProductCatalogService;
use App\Application\UseCase\SalesReportService;
use App\Infrastructure\Clock\SystemClock;
use App\Infrastructure\Persistence\LaravelUnitOfWork;
use App\Infrastructure\Persistence\Repository\EloquentCategoryRepository;
use App\Infrastructure\Persistence\Repository\EloquentProductRepository;
use App\Infrastructure\Persistence\Repository\EloquentSaleRepository;
use App\Infrastructure\Persistence\Repository\EloquentSalesReportQuery;
use App\Infrastructure\Persistence\Repository\EloquentUserRepository;
use App\Infrastructure\Security\Argon2PasswordHasher;
use App\Infrastructure\Security\JwtTokenGenerator;
use App\Infrastructure\Storage\LocalFileStorage;
use Illuminate\Support\ServiceProvider;

final class PortBindingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // 10 Outbound Ports -> Infrastructure Bindings
        $this->app->singleton(Clock::class, SystemClock::class);
        $this->app->singleton(UnitOfWork::class, LaravelUnitOfWork::class);
        $this->app->singleton(PasswordHasher::class, Argon2PasswordHasher::class);
        $this->app->singleton(TokenGenerator::class, JwtTokenGenerator::class);
        $this->app->singleton(FileStorage::class, LocalFileStorage::class);
        $this->app->singleton(CategoryRepository::class, EloquentCategoryRepository::class);
        $this->app->singleton(UserRepository::class, EloquentUserRepository::class);
        $this->app->singleton(ProductRepository::class, EloquentProductRepository::class);
        $this->app->singleton(SaleRepository::class, EloquentSaleRepository::class);
        $this->app->singleton(SalesReportQuery::class, EloquentSalesReportQuery::class);

        // 5 Inbound Ports -> Application Use Cases Bindings
        $this->app->singleton(Authenticate::class, AuthenticationService::class);
        $this->app->singleton(ManageProducts::class, ProductCatalogService::class);
        $this->app->singleton(PlaceSale::class, PlaceSaleService::class);
        $this->app->singleton(GetSales::class, GetSalesService::class);
        $this->app->singleton(GetSalesReport::class, SalesReportService::class);
    }
}