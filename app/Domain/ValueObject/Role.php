<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\InvalidRoleException;

enum Role: string
{
    case Admin = 'admin';
    case Seller = 'seller';

    public static function fromString(string $role): self
    {
        return match (strtolower(trim($role))) {
            'admin' => self::Admin,
            'seller' => self::Seller,
            default => throw new InvalidRoleException($role),
        };
    }
}