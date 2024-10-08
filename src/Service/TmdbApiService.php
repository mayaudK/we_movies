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
    const FIRST_BEST_MOVIE_ENDPOINT = '/movie/popular';
    private Client $client;
    private string $baseUrl;
    private string $apiBearerToken;

    public function __construct(string $baseUrl, string $apiBearerToken, KernelInterface $kernel)
    {
        $this->client = new Client(['verify' => $kernel->getProjectDir() . '/public/certs/cacert.pem']);
        $this->baseUrl = $baseUrl;
        $this->apiBearerToken = $apiBearerToken;
    }

    public function fetchTrailerByMovieId(int $movieId) : array
    {
        $videos = $this->fetchVideosByMovieId($movieId);
        foreach ($videos as $video) {
            if ($video['type'] == 'Trailer') {
                return $video;
            }
        }
        return ['error' => 'No trailer found.'];
    }

    public function fetchVideosByMovieId(int $movieId) : array
    {
        $response = $this->fetch('GET', '/movie/' . $movieId . '/videos' . '?' . self::RESULTS_LANGUAGE);
        $videos = json_decode($response->getContents(), true)['results'];
        return $videos;
    }

    public function fetchGenresFromApi()
    {
        $response = $this->fetch('GET', self::GENRES_ENDPOINT . '?' . self::RESULTS_LANGUAGE);
        $rawGenresById = json_decode($response->getContents(), true)['genres'];
        return array_column($rawGenresById, 'name', 'id');
    }

    public function fetchFirstBestMovie()
    {
        $response = $this->fetch('GET', self::FIRST_BEST_MOVIE_ENDPOINT . '?' . self::RESULTS_LANGUAGE . '&page=1');
        $bestMovie = json_decode($response->getContents(), true)['results'][0];

        $videos = $this->fetchTrailerByMovieId($bestMovie['id']);
        return [
            'title' => $bestMovie['title'],
            'video' => $videos,
            'overview' => $bestMovie['overview'],
            'original_title' => $bestMovie['original_title'],
            'release_date' => $bestMovie['release_date'],
            'vote_average' => $bestMovie['vote_average'],
        ];
    }

    public function fetchMoviesByGenre(string $genre) : array
    {
        $response = $this->fetch('GET', '/discover/movie?with_genres=' . $genre . '&' . self::RESULTS_LANGUAGE);
        $results = json_decode($response->getContents(), true)['results'];
        return $results;
    }

    public function searchByTitle(string $title) : array
    {
        $response = $this->fetch('GET', '/search/movie?query=' . $title . '&' . self::RESULTS_LANGUAGE);
        return json_decode($response->getContents(), true)['results'];
    }

    public function fetchMovieById(int $movieId) : array
    {
        $response = $this->fetch('GET', '/movie/' . $movieId . '?' . self::RESULTS_LANGUAGE);
        return json_decode($response->getContents(), true);
    }

    public function fetchTopRatedMovies() : array
    {
        $response = $this->fetch('GET', '/movie/top_rated?' . self::RESULTS_LANGUAGE);
        return json_decode($response->getContents(), true)['results'];
    }

    public function postRatingMovie(int $movieId, int $rating) : array
    {
        $response = $this->fetch('POST', '/movie/' . $movieId . '/rating', [
            'json' => [
                'value' => $rating,
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiBearerToken,
                'accept' => 'application/json',
            ],
        ]);
        return json_decode($response->getContents(), true);
    }

    public function fetch(string $method, string $endpoint, array $options = [], int $limit = 7) : array | StreamInterface
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