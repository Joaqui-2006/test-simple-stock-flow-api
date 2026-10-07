<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Model\PageRequest;
use App\Application\Ports\Inbound\ManageProducts;
use App\Application\Ports\Inbound\PagedResult;
use App\Application\Ports\Inbound\ProductView;
use App\Application\Ports\Outbound\CategoryRepository;
use App\Application\Ports\Outbound\Clock;
use App\Application\Ports\Outbound\FileStorage;
use App\Application\Ports\Outbound\ProductRepository;
use App\Domain\Exception\ProductNotFoundException;
use App\Domain\Exception\UnknownCategoryException;
use App\Domain\Model\Product;
use App\Domain\ValueObject\CategoryId;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\ProductId;

final class ProductCatalogService implements ManageProducts
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly FileStorage $fileStorage,
        private readonly Clock $clock
    ) {
    }

    public function listProducts(?string $search, ?string $categoryId, PageRequest $pageRequest): PagedResult
    {
        $catVO = $categoryId !== null ? new CategoryId($categoryId) : null;
        return $this->productRepository->listActive($search, $catVO, $pageRequest);
    }

    public function getProduct(string $id): ProductView
    {
        $product = $this->productRepository->findById(new ProductId($id));
        if ($product === null || $product->isDeleted()) {
            throw new ProductNotFoundException($id);
        }

        $category = $this->categoryRepository->findById($product->getCategoryId());
        $categoryName = $category !== null ? $category->getName() : 'General';

        return new ProductView(
            $product->getId()->toString(),
            $product->getName(),
            $product->getPrice()->toFloat(),
            $product->getPrice()->getCurrency(),
            $product->getStock(),
            $product->getCategoryId()->toString(),
            $categoryName,
            $product->getImageUrl()
        );
    }

    public function createProduct(string $name, float $price, string $currency, int $initialStock, string $categoryId): ProductView
    {
        $catVO = new CategoryId($categoryId);
        $category = $this->categoryRepository->findById($catVO);
        if ($category === null) {
            throw new UnknownCategoryException($categoryId);
        }

        $money = Money::strictlyPositive($price, $currency);
        $id = $this->productRepository->nextIdentity();

        $product = new Product(
            $id,
            trim($name),
            $money,
            $initialStock,
            $catVO
        );

        $this->productRepository->save($product);

        return new ProductView(
            $product->getId()->toString(),
            $product->getName(),
            $product->getPrice()->toFloat(),
            $product->getPrice()->getCurrency(),
            $product->getStock(),
            $product->getCategoryId()->toString(),
            $category->getName(),
            $product->getImageUrl()
        );
    }

    public function updateProduct(string $id, string $name, float $price, string $currency, string $categoryId): ProductView
    {
        $pid = new ProductId($id);
        $product = $this->productRepository->findById($pid);
        if ($product === null || $product->isDeleted()) {
            throw new ProductNotFoundException($id);
        }

        $catVO = new CategoryId($categoryId);
        $category = $this->categoryRepository->findById($catVO);
        if ($category === null) {
            throw new UnknownCategoryException($categoryId);
        }

        $money = Money::strictlyPositive($price, $currency);
        $product->updateDetails($name, $money, $catVO);

        $this->productRepository->save($product);

        return new ProductView(
            $product->getId()->toString(),
            $product->getName(),
            $product->getPrice()->toFloat(),
            $product->getPrice()->getCurrency(),
            $product->getStock(),
            $product->getCategoryId()->toString(),
            $category->getName(),
            $product->getImageUrl()
        );
    }

    public function deleteProduct(string $id): void
    {
        $pid = new ProductId($id);
        $product = $this->productRepository->findById($pid);
        if ($product === null || $product->isDeleted()) {
            throw new ProductNotFoundException($id);
        }

        $product->markAsDeleted($this->clock->now());
        $this->productRepository->save($product);
    }

    public function setProductImage(string $id, string $binaryContent, string $mimeType): string
    {
        $pid = new ProductId($id);
        $product = $this->productRepository->findById($pid);
        if ($product === null || $product->isDeleted()) {
            throw new ProductNotFoundException($id);
        }

        $ext = str_contains($mimeType, 'png') ? 'png' : 'jpg';
        $key = $this->fileStorage->put($binaryContent, $ext);
        $imageUrl = '/media/' . $key;

        $product->setImageUrl($imageUrl);
        $this->productRepository->save($product);

        return $imageUrl;
    }

    public function listCategories(): array
    {
        $categories = $this->categoryRepository->findAll();
        return array_map(fn($c) => [
            'id' => $c->getId()->toString(),
            'name' => $c->getName()
        ], $categories);
    }

    public function getMediaBinary(string $key): ?array
    {
        return $this->fileStorage->get($key);
    }
}