<?php

namespace App\Tests\Service;

use App\Service\TmdbApiService;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class TmdbApiServiceTest extends TestCase
{
    private TmdbApiService $tmdbApiService;
    private Client $client;
    private KernelInterface $kernel;

    protected function setUp(): void
    {
        $this->client = $this->createMock(Client::class);
        $this->kernel = $this->createMock(KernelInterface::class);
        $this->tmdbApiService = new TmdbApiService('api_key', 'base_url', 'api_bearer_token', $this->kernel);
    }

    public function testFetchGenresFromApi()
    {
        $response = $this->createMock(StreamInterface::class);
        $response->method('getContents')->willReturn(json_encode(['genres' => [['id' => 1, 'name' => 'Action']]]));

        $this->client->method('request')->willReturn($response);

        $genres = $this->tmdbApiService->fetchGenresFromApi();
        $this->assertEquals(['1' => 'Action'], $genres);
    }

    public function testFetchFirstBestMovie()
    {
        $response = $this->createMock(StreamInterface::class);
        $response->method('getContents')->willReturn(json_encode(['results' => [['title' => 'Movie', 'overview' => 'Overview', 'release_date' => '2023-01-01', 'vote_average' => 8.5]]]));

        $this->client->method('request')->willReturn($response);

        $movie = $this->tmdbApiService->fetchFirstBestMovie();
        $this->assertEquals('Movie', $movie['title']);
    }
}