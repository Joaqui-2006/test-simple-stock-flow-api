<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use App\Application\Model\PageRequest;
use App\Application\Ports\Inbound\ManageProducts;
use App\Presentation\Http\Request\CreateProductRequest;
use App\Presentation\Http\Request\UpdateProductRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class ProductController
{
    public function __construct(
        private readonly ManageProducts $productService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $page = (int) $request->query('page', 1);
        $pageSize = (int) $request->query('pageSize', 20);
        $search = $request->query('search');
        $categoryId = $request->query('categoryId');

        $result = $this->productService->listProducts(
            $search !== null ? (string) $search : null,
            $categoryId !== null ? (string) $categoryId : null,
            new PageRequest($page, $pageSize)
        );

        return response()->json([
            'items' => array_map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'price' => $p->price,
                'currency' => $p->currency,
                'stock' => $p->stock,
                'categoryId' => $p->categoryId,
                'categoryName' => $p->categoryName,
                'imageUrl' => $p->imageUrl,
            ], $result->items),
            'total' => $result->total,
            'page' => $result->page,
            'pageSize' => $result->pageSize,
            'totalPages' => $result->totalPages,
        ], 200);
    }

    public function show(string $id): JsonResponse
    {
        $p = $this->productService->getProduct($id);
        return response()->json([
            'id' => $p->id,
            'name' => $p->name,
            'price' => $p->price,
            'currency' => $p->currency,
            'stock' => $p->stock,
            'categoryId' => $p->categoryId,
            'categoryName' => $p->categoryName,
            'imageUrl' => $p->imageUrl,
        ], 200);
    }

    public function store(CreateProductRequest $request): JsonResponse
    {
        $p = $this->productService->createProduct(
            (string) $request->validated('name'),
            (float) $request->validated('price'),
            (string) ($request->validated('currency') ?? 'COP'),
            (int) $request->validated('initialStock'),
            (string) $request->validated('categoryId')
        );

        return response()->json([
            'id' => $p->id,
            'name' => $p->name,
            'price' => $p->price,
            'currency' => $p->currency,
            'stock' => $p->stock,
            'categoryId' => $p->categoryId,
            'categoryName' => $p->categoryName,
            'imageUrl' => $p->imageUrl,
        ], 201);
    }

    public function update(string $id, UpdateProductRequest $request): JsonResponse
    {
        $p = $this->productService->updateProduct(
            $id,
            (string) $request->validated('name'),
            (float) $request->validated('price'),
            (string) ($request->validated('currency') ?? 'COP'),
            (string) $request->validated('categoryId')
        );

        return response()->json([
            'id' => $p->id,
            'name' => $p->name,
            'price' => $p->price,
            'currency' => $p->currency,
            'stock' => $p->stock,
            'categoryId' => $p->categoryId,
            'categoryName' => $p->categoryName,
            'imageUrl' => $p->imageUrl,
        ], 200);
    }

    public function destroy(string $id): Response
    {
        $this->productService->deleteProduct($id);
        return response('', 204, ['Content-Length' => '0']);
    }

    public function uploadImage(string $id, Request $request): JsonResponse
    {
        $file = $request->file('file');
        if ($file === null) {
            return response()->json([
                'type' => 'about:blank',
                'title' => 'Bad Request',
                'status' => 400,
                'detail' => 'Archivo no proporcionado en la solicitud',
                'errors' => ['file' => ['Se requiere un archivo de imagen.']]
            ], 400, ['Content-Type' => 'application/problem+json']);
        }

        $imageUrl = $this->productService->setProductImage(
            $id,
            file_get_contents($file->getRealPath()),
            $file->getClientMimeType()
        );

        return response()->json(['imageUrl' => $imageUrl], 200);
    }
}