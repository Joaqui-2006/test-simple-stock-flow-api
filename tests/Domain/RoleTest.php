<?php

declare(strict_types=1);

namespace Tests\Domain;

use App\Domain\ValueObject\Role;
use PHPUnit\Framework\TestCase;

final class RoleTest extends TestCase
{
    public function testOnlyAdminAndSellerExist(): void
    {
        $this->assertSame('admin', Role::Admin->value);
        $this->assertSame('seller', Role::Seller->value);

        $cases = Role::cases();
        $this->assertCount(2, $cases);
    }
}
