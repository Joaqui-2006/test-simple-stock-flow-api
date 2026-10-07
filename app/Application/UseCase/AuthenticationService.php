<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Ports\Inbound\Authenticate;
use App\Application\Ports\Inbound\AuthResult;
use App\Application\Ports\Outbound\PasswordHasher;
use App\Application\Ports\Outbound\TokenGenerator;
use App\Application\Ports\Outbound\UserRepository;
use App\Domain\Exception\DuplicateUsernameException;
use App\Domain\Exception\InvalidCredentialsException;
use App\Domain\Model\User;
use App\Domain\ValueObject\Role;
use App\Domain\ValueObject\UserId;
use App\Domain\ValueObject\Username;
use Ramsey\Uuid\Uuid;

final class AuthenticationService implements Authenticate
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PasswordHasher $passwordHasher,
        private readonly TokenGenerator $tokenGenerator
    ) {
    }

    public function login(string $username, string $password): AuthResult
    {
        $userVO = new Username($username);
        $user = $this->userRepository->findByUsername($userVO);

        if ($user === null || !$this->passwordHasher->verify($password, $user->getPasswordHash())) {
            throw new InvalidCredentialsException('Credenciales de acceso no válidas');
        }

        $token = $this->tokenGenerator->generate(
            $user->getId()->toString(),
            $user->getUsername()->toString(),
            $user->getRole()->value
        );

        return new AuthResult(
            $token,
            $user->getId()->toString(),
            $user->getUsername()->toString(),
            $user->getRole()->value
        );
    }

    public function registerSeller(string $username, string $password): array
    {
        $userVO = new Username($username);
        $existing = $this->userRepository->findByUsername($userVO);
        if ($existing !== null) {
            throw new DuplicateUsernameException($username);
        }

        $userId = new UserId(Uuid::uuid4()->toString());
        $hash = $this->passwordHasher->hash($password);
        $seller = new User($userId, $userVO, $hash, Role::Seller);

        $this->userRepository->save($seller);

        return [
            'id' => $seller->getId()->toString(),
            'username' => $seller->getUsername()->toString(),
            'role' => $seller->getRole()->value,
        ];
    }
}