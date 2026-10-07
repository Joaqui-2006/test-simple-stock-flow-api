<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\ValueObject\Role;
use App\Domain\ValueObject\UserId;
use App\Domain\ValueObject\Username;

final class User
{
    public function __construct(
        private readonly UserId $id,
        private readonly Username $username,
        private string $passwordHash,
        private readonly Role $role
    ) {
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getUsername(): Username
    {
        return $this->username;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getRole(): Role
    {
        return $this->role;
    }
}