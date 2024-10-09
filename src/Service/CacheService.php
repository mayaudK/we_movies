<?php

namespace App\Service;

use Psr\Cache\CacheItemPoolInterface;

class CacheService
{
    private CacheItemPoolInterface $cache;
    const ONE_HOUR_CACHE_IN_SECONDS = 3600;

    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    public function getItemsFromCache(string $cacheKey, callable $fetchItemCallback)
    {
        $cachedItem = $this->cache->getItem($cacheKey);
        if (!$cachedItem->isHit()) {
            $itemContent = $fetchItemCallback();
            $this->cacheItem($cachedItem, $itemContent);
        } else {
            $itemContent = $cachedItem->get();
        }

        return $itemContent;
    }

    private function cacheItem($cachedItem, $itemContent)
    {
        $cachedItem->set($itemContent);
        $cachedItem->expiresAfter(self::ONE_HOUR_CACHE_IN_SECONDS);
        $this->cache->save($cachedItem);
    }
}