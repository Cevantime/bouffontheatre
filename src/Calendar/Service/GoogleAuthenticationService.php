<?php

namespace App\Calendar\Service;

use _PHPStan_7c8075089\Nette\Neon\Exception;
use App\Service\ConfigService;
use League\OAuth2\Client\Grant\RefreshToken;
use League\OAuth2\Client\Provider\Google;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;

class GoogleAuthenticationService
{
    const AUTH_SCOPE_URL = 'https://www.googleapis.com/auth/calendar';
    const OAUTH_URL = 'https://accounts.google.com/o/oauth2/auth';
    const OAUTH2_STATE = 'oauth2state';

    private $google;

    public function __construct(
        private RequestStack  $stack,
        private ConfigService $config,
        ParameterBagInterface $parameterBag
    )
    {
        $this->google = new Google([
                'clientId' => $parameterBag->get('google_calendar_client_id'),
                'clientSecret' => $parameterBag->get('google_calendar_client_secret'),
                'redirectUri' => $parameterBag->get('google_calendar_redirect_uri'),
                'access_type' => 'offline'
            ]
        );
    }

    public function refreshTokenIfNeeded()
    {
        if (!$this->config->hasValue(ConfigService::EXPIRES_AT) || !$this->config->hasValue(ConfigService::REFRESH_TOKEN)) {
            return;
        }
        $expiredAt = $this->config->getValue(ConfigService::EXPIRES_AT);
        if (time() - 100 >= $expiredAt) {
            $token = $this->google->getAccessToken(new RefreshToken(), [
                'refresh_token' => $this->config->getValue(ConfigService::REFRESH_TOKEN),
                'access_type' => 'offline'
            ]);
            $this->saveToken($token);
        }
    }

    public function goToAuthUrl()
    {
        $url = $this->google->getAuthorizationUrl([
            'prompt' => 'consent',
            'access_type' => 'offline',
            'scope' => [self::AUTH_SCOPE_URL]
        ]);
        $this->getSession()->set(self::OAUTH2_STATE, $this->google->getState());
        return new RedirectResponse($url);
    }

    public function checkState($state)
    {
        return $state !== null && $this->getSession()->get(self::OAUTH2_STATE) === $state;
    }

    public function getSession()
    {
        return $this->stack->getSession();
    }

    public function unsetState()
    {
        $this->getSession()->remove(self::OAUTH2_STATE);
    }

    public function getAccessToken($code)
    {
        $token = $this->google->getAccessToken('authorization_code', [
            'code' => $code
        ]);

        $this->saveToken($token);
    }

    private function saveToken($token)
    {
        if (!$token) {
            throw new Exception('Unable to obtain access token');
        }

        $this->config->saveConfigs([
            ConfigService::ACCESS_TOKEN => $token->getToken(),
            ConfigService::EXPIRES_AT => $token->getExpires()
        ]);

        if($token->getRefreshToken()) {
            $this->config->saveConfig(ConfigService::REFRESH_TOKEN, $token->getRefreshToken());
        }
    }
}
