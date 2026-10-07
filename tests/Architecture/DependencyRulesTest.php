<?php

declare(strict_types=1);

namespace Tests\Architecture;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;

final class DependencyRulesTest extends TestCase
{
    /**
     * R-01: Domain no importa nada de terceros ni de Laravel (Illuminate)
     */
    public function testDomainHasZeroIlluminateImports(): void
    {
        $domainPath = __DIR__ . '/../../app/Domain';
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($domainPath));

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $content = file_get_contents($file->getPathname());
                $this->assertStringNotContainsString(
                    'Illuminate\\',
                    $content,
                    sprintf('ViolaciÃ³n R-01 detectada en: %s', $file->getPathname())
                );
            }
        }
    }

    /**
     * R-03: Presentation no importa Infrastructure
     */
    public function testPresentationDoesNotImportInfrastructure(): void
    {
        $presPath = __DIR__ . '/../../app/Presentation';
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($presPath));

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $content = file_get_contents($file->getPathname());
                $this->assertStringNotContainsString(
                    'App\\Infrastructure',
                    $content,
                    sprintf('ViolaciÃ³n R-03 detectada en: %s', $file->getPathname())
                );
            }
        }
    }
}