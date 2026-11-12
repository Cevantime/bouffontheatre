<?php

namespace App\Security;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

/**
 * Authenticates the Quotas tool, the only client of /api/quotas, with a shared secret sent as a bearer token.
 * Without a long enough QUOTAS_API_TOKEN the API refuses everybody.
 */
final class QuotasTokenHandler implements AccessTokenHandlerInterface
{
    public const ROLE = 'ROLE_QUOTAS_API';
    private const MIN_TOKEN_LENGTH = 32;

    public function __construct(#[Autowire('%env(QUOTAS_API_TOKEN)%')] private readonly string $expectedToken)
    {
    }

    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
    {
        if (strlen($this->expectedToken) < self::MIN_TOKEN_LENGTH || !hash_equals(hash('sha256', $this->expectedToken), hash('sha256', $accessToken))) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        return new UserBadge('quotas', static fn (): InMemoryUser => new InMemoryUser('quotas', null, [self::ROLE]));
    }
}
