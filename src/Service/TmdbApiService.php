<?php

namespace App\Service;

use App\Interface\ApiInterface;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use GuzzleHttp\Client;

class TmdbApiService implements ApiInterface
{
    const GENRES_ENDPOINT = '/genre/movie/list';
    const RESULTS_LANGUAGE = 'language=en';
    private Client $client;

    private string $apiKey;
    private string $baseUrl;
    private string $apiBearerToken;

    public function __construct(string $apiKey, string $baseUrl, string $apiBearerToken, KernelInterface $kernel)
    {
        $this->client = new Client(['verify' => $kernel->getProjectDir() . '/public/certs/cacert.pem']);
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
        $this->apiBearerToken = $apiBearerToken;
    }

    public function getMoviesByGenre(string $genre)
    {
        return $this->fetch('GET', '/movies?genre=' . $genre);
    }

    public function fetchGenresFromApi()
    {
        $response = $this->fetch('GET', self::GENRES_ENDPOINT . '?' . self::RESULTS_LANGUAGE);
        $rawGenresById = json_decode($response->getContents(), true)['genres'];
        return array_column($rawGenresById, 'name', 'id');
    }

    public function fetch(string $method, string $endpoint, array $options = []) : array | StreamInterface
    {
        if (empty($options)) {
            $options = [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiBearerToken,
                    'accept' => 'application/json',
                ],
            ];
        }
        try {
            $response = $this->client->request(
                $method,
                $this->baseUrl . $endpoint,
                $options
            );

            return $response->getBody();
        }  catch (GuzzleException $e) {
            return ['error' => $e->getMessage() . ' from guzzle exception.'];
        }   catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}