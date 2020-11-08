<?php

namespace aivus\XML2Spreadsheet\Google\DTO;

/**
 * This class allows us to decouple code which uses access token data from Google client implementation
 */
class AccessTokenHolder
{
    private ?string $accessToken;
    private ?int $expiresIn;
    private ?string $scope;
    private ?string $tokenType;
    private ?int $created;

    private function __construct()
    {
    }

    /**
     * Create access token holder DTO based on the array representation
     */
    public static function create(?array $accessToken): self
    {
        $holder = new static;
        $holder->accessToken = $accessToken['access_token'] ?? null;
        $holder->expiresIn = $accessToken['expires_in'] ?? null;
        $holder->scope = $accessToken['scope'] ?? null;
        $holder->tokenType = $accessToken['token_type'] ?? null;
        $holder->created = $accessToken['created'] ?? null;

        return $holder;
    }

    public function setAccessToken(?string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function getExpiresIn(): ?int
    {
        return $this->expiresIn;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function getTokenType(): ?string
    {
        return $this->tokenType;
    }

    public function getCreated(): ?int
    {
        return $this->created;
    }
}
