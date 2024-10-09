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

    public function getGenresFromCache(string $cacheKey, callable $fetchGenresCallback)
    {
        $cachedItem = $this->cache->getItem($cacheKey);
        if (!$cachedItem->isHit()) {
            $genresById = $fetchGenresCallback();
            $this->cacheGenres($cachedItem, $genresById);
        } else {
            $genresById = $cachedItem->get();
        }

        return $genresById;
    }

    private function cacheGenres($cachedItem, $genresById)
    {
        $cachedItem->set($genresById);
        $cachedItem->expiresAfter(self::ONE_HOUR_CACHE_IN_SECONDS);
        $this->cache->save($cachedItem);
    }
}