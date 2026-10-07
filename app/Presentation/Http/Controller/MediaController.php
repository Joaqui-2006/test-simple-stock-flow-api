<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use App\Application\Ports\Inbound\ManageProducts;
use Symfony\Component\HttpFoundation\Response;

final class MediaController
{
    public function __construct(
        private readonly ManageProducts $productService
    ) {
    }

    public function show(string $key): Response
    {
        $binary = $this->productService->getMediaBinary($key);
        if ($binary === null) {
            return response('', 404, ['Content-Length' => '0']);
        }

        return response($binary['content'], 200, [
            'Content-Type' => $binary['mimeType'],
        ]);
    }
}