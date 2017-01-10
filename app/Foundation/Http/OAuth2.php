<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Foundation\Http;

use Jitamin\Foundation\Base;

/**
 * OAuth2 Client.
 */
class OAuth2 extends Base
{
    protected $clientId;
    protected $secret;
    protected $callbackUrl;
    protected $authUrl;
    protected $tokenUrl;
    protected $scopes;
    protected $tokenType;
    protected $accessToken;

    /**
     * Create OAuth2 service.
     *
     * @param string $clientId
     * @param string $secret
     * @param string $callbackUrl
     * @param string $authUrl
     * @param string $tokenUrl
     * @param array  $scopes
     *
     * @return OAuth2
     */
    public function createService($clientId, $secret, $callbackUrl, $authUrl, $tokenUrl, array $scopes)
    {
        $this->clientId = $clientId;
        $this->secret = $secret;
        $this->callbackUrl = $callbackUrl;
        $this->authUrl = $authUrl;
        $this->tokenUrl = $tokenUrl;
        $this->scopes = $scopes;

        return $this;
    }

    /**
     * Generate OAuth2 state and return the token value.
     *
     * @return string
     */
    public function getState()
    {
        if (!isset($this->sessionStorage->oauthState) || empty($this->sessionStorage->oauthState)) {
            $this->sessionStorage->oauthState = $this->token->getToken();
        }

        return $this->sessionStorage->oauthState;
    }

    /**
     * Check the validity of the state (CSRF token).
     *
     * @param string $state
     *
     * @return bool
     */
    public function isValidateState($state)
    {
        return $state === $this->getState();
    }

    /**
     * Get authorization url.
     *
     * @return string
     */
    public function getAuthorizationUrl()
    {
        $params = [
            'response_type' => 'code',
            'client_id'     => $this->clientId,
            'redirect_uri'  => $this->callbackUrl,
            'scope'         => implode(' ', $this->scopes),
            'state'         => $this->getState(),
        ];

        return $this->authUrl.'?'.http_build_query($params);
    }

    /**
     * Get authorization header.
     *
     * @return string
     */
    public function getAuthorizationHeader()
    {
        if (strtolower($this->tokenType) === 'bearer') {
            return 'Authorization: Bearer '.$this->accessToken;
        }

        return '';
    }

    /**
     * Get access token.
     *
     * @param string $code
     *
     * @return string
     */
    public function getAccessToken($code)
    {
        if (empty($this->accessToken) && !empty($code)) {
            $params = [
                'code'          => $code,
                'client_id'     => $this->clientId,
                'client_secret' => $this->secret,
                'redirect_uri'  => $this->callbackUrl,
                'grant_type'    => 'authorization_code',
                'state'         => $this->getState(),
            ];

            $response = json_decode($this->httpClient->postForm($this->tokenUrl, $params, ['Accept: application/json']), true);

            $this->tokenType = isset($response['token_type']) ? $response['token_type'] : '';
            $this->accessToken = isset($response['access_token']) ? $response['access_token'] : '';
        }

        return $this->accessToken;
    }

    /**
     * Set access token.
     *
     * @param string $token
     * @param string $type
     *
     * @return string
     */
    public function setAccessToken($token, $type = 'bearer')
    {
        $this->accessToken = $token;
        $this->tokenType = $type;
    }
}
