<?php

namespace aivus\XML2Spreadsheet;

use aivus\XML2Spreadsheet\Google\DTO\AccessTokenHolder;

/**
 * Application context
 */
class Context
{
    private array $options = [];
    private ?AccessTokenHolder $accessTokenHolder = null;
    private ?string $parserName = null;

    public function setOption(string $key, $value): void
    {
        $this->options[$key] = $value;
    }

    /**
     * @return mixed|null If exists return value from the context, null otherwise
     */
    public function getOption(string $key, $default = null)
    {
        return $this->options[$key] ?? $default;
    }

    public function setAccessTokenHolder(AccessTokenHolder $accessTokenHolder): void
    {
        $this->accessTokenHolder = $accessTokenHolder;
    }

    public function getAccessTokenHolder(): ?AccessTokenHolder
    {
        return $this->accessTokenHolder;
    }

    public function setParserName(string $name): void
    {
        $this->parserName = $name;
    }

    public function getParserName(): ?string
    {
        return $this->parserName;
    }
}
