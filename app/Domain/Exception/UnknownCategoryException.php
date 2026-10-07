<?php

declare(strict_types=1);

namespace App\Domain\Exception;

final class UnknownCategoryException extends BusinessRuleViolation
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('CategorÃ­a desconocida o no vÃ¡lida: %s', $id));
    }
}