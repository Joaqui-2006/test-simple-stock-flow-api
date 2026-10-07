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
                    sprintf('Violación R-01 detectada en: %s', $file->getPathname())
                );
            }
        }
    }

    /**
     * R-02: Application no importa Infrastructure ni Presentation ni Illuminate
     */
    public function testApplicationDoesNotImportInfrastructureOrPresentation(): void
    {
        $appPath = __DIR__ . '/../../app/Application';
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($appPath));

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $content = file_get_contents($file->getPathname());
                $this->assertStringNotContainsString(
                    'App\\Infrastructure',
                    $content,
                    sprintf('Violación R-02 detectada en: %s (Application no puede importar Infrastructure)', $file->getPathname())
                );
                $this->assertStringNotContainsString(
                    'App\\Presentation',
                    $content,
                    sprintf('Violación R-02 detectada en: %s (Application no puede importar Presentation)', $file->getPathname())
                );
                $this->assertStringNotContainsString(
                    'Illuminate\\',
                    $content,
                    sprintf('Violación R-02 detectada en: %s (Application no puede importar Illuminate)', $file->getPathname())
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
                    sprintf('Violación R-03 detectada en: %s', $file->getPathname())
                );
            }
        }
    }

    /**
     * R-05: Controladores de Presentation solo reciben puertos Inbound, nunca Outbound
     */
    public function testPresentationControllersOnlyReceiveInboundPorts(): void
    {
        $controllerPath = __DIR__ . '/../../app/Presentation/Http/Controller';
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($controllerPath));

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $content = file_get_contents($file->getPathname());
                $this->assertStringNotContainsString(
                    'App\\Application\\Ports\\Outbound',
                    $content,
                    sprintf('Violación R-05 detectada en: %s (Controlador no puede importar Outbound ports)', $file->getPathname())
                );
            }
        }
    }
}