<?php

namespace TobiasOlry\TalklyBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * @author David Badura <david.badura@i22.de>
 */
class NtlmAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @param bool $enabled
     * @param string $domain
     * @param bool $debug
     */
    public function __construct($enabled, $domain, $debug = false)
    {
        $this->enabled = $enabled;
        $this->domain = $domain;
        $this->debug = $debug;
    }

    /**
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return null|Response
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return null;
    }

    /**
     * @param Request $request
     * @return array|null
     */
    public function getCredentials(Request $request)
    {
        if (!$this->enabled) {
            return null;
        }

        if ($this->debug) {
            return [
                'username' => 'musterman'
            ];
        }

        if (!$username = $request->server->get('REMOTE_USER')) {
            return null;
        }

        return [
            'username' => $username
        ];
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return UserInterface
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $username = str_replace($this->domain . '\\', '', $credentials['username']);

        return $userProvider->loadUserByUsername($username);
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        if ($this->debug) {
            return true;
        }

        if (!$credentials['username']) {
            return false;
        }

        if (strpos($credentials['username'], $this->domain . '\\') !== 0) {
            return false;
        }

        return true;
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return null;
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    /**
     * @return bool
     */
    public function supportsRememberMe()
    {
        return false;
    }
}