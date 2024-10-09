<?php

namespace App\Service;

use Psr\Cache\CacheItemPoolInterface;

class AutocompleteService
{
    private CacheService $cacheService;

    public function __construct(TmdbApiService $tmdbApiService, CacheService $cacheService)
    {
        $this->tmdbApiService = $tmdbApiService;
        $this->cacheService = $cacheService;
    }

    public function getAutocompleteSuggestions(string $query) : array {
        $autocompleteCacheKey = 'autocomplete_' . $query;
        return $this->cacheService->getItemsFromCache($autocompleteCacheKey, [$this->tmdbApiService, 'searchByTitle'], $query);
    }
}
