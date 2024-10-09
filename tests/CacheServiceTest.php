<?php

namespace App\Tests\Service;

use App\Service\CacheService;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CacheServiceTest extends TestCase
{
    private CacheService $cacheService;
    private CacheItemPoolInterface $cachePool;
    private CacheItemInterface $cacheItem;

    protected function setUp(): void
    {
        $this->cachePool = $this->createMock(CacheItemPoolInterface::class);
        $this->cacheItem = $this->createMock(CacheItemInterface::class);
        $this->cacheService = new CacheService($this->cachePool);
    }

    public function testgetItemsFromCache()
    {
        $this->cachePool->method('getItem')->willReturn($this->cacheItem);
        $this->cacheItem->method('isHit')->willReturn(false);
        $this->cacheItem->method('get')->willReturn(['1' => 'Action']);

        $fetchItemCallback = function() {
            return ['1' => 'Action'];
        };

        $genres = $this->cacheService->getItemsFromCache('cache_key', $fetchItemCallback);
        $this->assertEquals(['1' => 'Action'], $genres);
    }
}