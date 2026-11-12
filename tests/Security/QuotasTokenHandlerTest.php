<?php

namespace App\Tests\Security;

use App\Security\QuotasTokenHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class QuotasTokenHandlerTest extends TestCase
{
    private const TOKEN = 'a3f9c1d27b5e48a0963d1c7e5f2b8a04d6e9137c5b2a8f40e1d3c6b9a7f20514';

    public function testAcceptsTheExpectedTokenAsTheQuotasUser(): void
    {
        $badge = (new QuotasTokenHandler(self::TOKEN))->getUserBadgeFrom(self::TOKEN);

        $user = $badge->getUserLoader()($badge->getUserIdentifier());
        $this->assertSame('quotas', $user->getUserIdentifier());
        $this->assertSame([QuotasTokenHandler::ROLE], $user->getRoles());
    }

    /** @dataProvider rejected */
    public function testRejectsAnythingElse(string $configured, string $given): void
    {
        $this->expectException(BadCredentialsException::class);

        (new QuotasTokenHandler($configured))->getUserBadgeFrom($given);
    }

    public static function rejected(): iterable
    {
        yield 'wrong token' => [self::TOKEN, 'nope'];
        yield 'token not configured' => ['', ''];
        yield 'configured token too short to be safe' => ['short', 'short'];
    }
}
