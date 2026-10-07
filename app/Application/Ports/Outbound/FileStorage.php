<?php

declare(strict_types=1);

namespace App\Application\Ports\Outbound;

interface FileStorage
{
    public function put(string $binaryContent, string $extension): string;
    public function get(string $key): ?array;
    public function delete(string $key): void;
}