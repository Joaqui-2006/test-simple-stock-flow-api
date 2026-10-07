<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Application\Ports\Outbound\UserRepository;
use App\Domain\Model\User;
use App\Domain\ValueObject\UserId;
use App\Domain\ValueObject\Username;
use App\Infrastructure\Persistence\Mapper\UserMapper;
use App\Infrastructure\Persistence\Model\UserModel;

final class EloquentUserRepository implements UserRepository
{
    public function findById(UserId $id): ?User
    {
        $model = UserModel::find($id->toString());
        return $model !== null ? UserMapper::toDomain($model) : null;
    }

    public function findByUsername(Username $username): ?User
    {
        $model = UserModel::where('username', $username->toString())->first();
        return $model !== null ? UserMapper::toDomain($model) : null;
    }

    public function save(User $user): void
    {
        UserModel::updateOrCreate(
            ['id' => $user->getId()->toString()],
            [
                'username' => $user->getUsername()->toString(),
                'password_hash' => $user->getPasswordHash(),
                'role' => $user->getRole()->value,
            ]
        );
    }
}