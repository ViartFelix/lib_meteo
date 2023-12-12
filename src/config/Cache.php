<?php

namespace Viartfelix\Freather\config;

use Phpfastcache\CacheManager;
use Phpfastcache\Config\ConfigurationOption;

use Phpfastcache\Helper\Psr16Adapter;
use Viartfelix\Freather\Exceptions\FreatherException;

class Cache {
    /** @var int $cacheDuration The duration of the cache. Default value: -1 second (no cache) */
    private int $cacheDuration = -1;

    private Psr16Adapter $Instance;

    function __construct(int &$cacheDuration=null)
    {
        $this->setCacheDuration($cacheDuration ?? -1);
        $this->setInstance(new Psr16Adapter('Files'));
    }

    public function getCacheDuration(): int|null
    {
        return $this->cacheDuration ?? null;
    }

    public function setCacheDuration(int $duration): void
    {
        $this->cacheDuration = $duration;
    }

    public function getInstance(): Psr16Adapter|null
    {
        return $this->Instance;
    }

    public function setInstance(Psr16Adapter $instance): void
    {
        $this->Instance = $instance;
    }

    public function checkItem(string $key): bool
    {
        return $this->getInstance()->has($key);
    }

    public function setItem(string $key, mixed $value): void
    {
        $this->getInstance()->set($key, $value, $this->getCacheDuration());
    }

    public function getItem(string $key): mixed
    {
        return $this->getInstance()->get($key);
    }
}


?>