<?php

namespace App\Service;

use Psr\Cache\CacheItemPoolInterface;

class AutocompleteService
{
    private $client;
    private $cache;
    private $apiKey;

    public function __construct(TmdbApiService $tmdbApiService, CacheItemPoolInterface $cache)
    {
        $this->tmdbApiService = $tmdbApiService;
        $this->cache = $cache;
    }

    public function getAutocompleteSuggestions(string $query) : array {
        $cacheKey = 'autocomplete_' . md5($query);
        $cachedItem = $this->cache->getItem($cacheKey);

        if (!$cachedItem->isHit()) {
            $data = $this->tmdbApiService->searchByTitle($query);

            $cachedItem->set($data);
            $cachedItem->expiresAfter(3600); // Cache for 1 hour
            $this->cache->save($cachedItem);
        } else {
            $data = $cachedItem->get();
        }

        return $data;
    }
}
