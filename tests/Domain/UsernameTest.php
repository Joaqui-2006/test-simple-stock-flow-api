<?php

declare(strict_types=1);

namespace Tests\Domain;

use App\Domain\ValueObject\Username;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class UsernameTest extends TestCase
{
    public function testNormalizesToLowercaseAndTrims(): void
    {
        $u = new Username('  AdminUser_123  ');
        $this->assertSame('adminuser_123', $u->toString());
    }

    public function testEmptyUsernameThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Username('   ');
    }
}
