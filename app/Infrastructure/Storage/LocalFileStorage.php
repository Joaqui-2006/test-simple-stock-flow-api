<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use App\Application\Ports\Outbound\FileStorage;
use Ramsey\Uuid\Uuid;

final class LocalFileStorage implements FileStorage
{
    private string $storageDir;

    public function __construct(?string $storageDir = null)
    {
        $this->storageDir = $storageDir ?? storage_path('app/media');
        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0755, true);
        }
    }

    public function put(string $binaryContent, string $extension): string
    {
        $key = Uuid::uuid4()->toString() . '.' . ltrim($extension, '.');
        file_put_contents($this->storageDir . DIRECTORY_SEPARATOR . $key, $binaryContent);
        return $key;
    }

    public function get(string $key): ?array
    {
        $safeKey = basename($key);
        $path = $this->storageDir . DIRECTORY_SEPARATOR . $safeKey;
        if (!file_exists($path)) {
            return null;
        }

        $content = file_get_contents($path);
        $mime = str_ends_with($safeKey, '.png') ? 'image/png' : 'image/jpeg';

        return [
            'content' => $content,
            'mimeType' => $mime,
        ];
    }

    public function delete(string $key): void
    {
        $safeKey = basename($key);
        $path = $this->storageDir . DIRECTORY_SEPARATOR . $safeKey;
        if (file_exists($path)) {
            unlink($path);
        }
    }
}