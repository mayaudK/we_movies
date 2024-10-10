<?php

namespace App\Controller;

use App\Service\AutocompleteService;
use App\Service\CacheService;
use App\Service\TmdbApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TmdbController extends AbstractController
{
    #[Route('/', name: 'app_tmdb')]
    public function index(TmdbApiService $tmdbService, CacheService $cacheService): Response
    {
        $cacheGenreKey = 'tmdb_genres';
        $cacheFirstBestMovieKey = 'tmdb_first_best_movie';
        $genres = $cacheService->getItemsFromCache($cacheGenreKey, [$tmdbService, 'fetchGenresFromApi']);
        $firstBestMovie = $cacheService->getItemsFromCache($cacheFirstBestMovieKey, [$tmdbService, 'fetchFirstBestMovie']);
        return $this->render('tmdb/index.html.twig', [
            'first_best_movie' => $firstBestMovie,
            'genres' => $genres,
            'movies' => $cacheService->getItemsFromCache('tmdb_top_rated_movies', [$tmdbService, 'fetchTopRatedMovies']),
        ]);
    }

    #[Route('/autocomplete', name: 'app_autocomplete')]
    public function autocomplete(Request $request, AutocompleteService $autocompleteService, CacheService $cacheService): Response
    {
        $query = $request->query->get('query', '');
        $results = $autocompleteService->getAutocompleteSuggestions($query);

        return $this->json($results);
    }
    #[Route('/moviesByGenre/{genre}', name: 'app_movies_by_genre')]
    public function getMoviesByGenre(Request $request, TmdbApiService $tmdbService, CacheService $cacheService, string $genre): Response
    {
        $movies = $cacheService->getItemsFromCache('tmdb_movies_by_genre' . $genre, function() use ($tmdbService, $genre) {
            return $tmdbService->fetchMoviesByGenre($genre);
        });
        return $this->json($movies);
    }

    #[Route('/movie/{movieId}', name: 'app_movie_details')]
    public function getMovieDetails(TmdbApiService $tmdbService, CacheService $cacheService, int $movieId): Response
    {
        $movie = $cacheService->getItemsFromCache('tmdb_movie_details' . $movieId, function() use ($tmdbService, $movieId) {
            return $tmdbService->fetchMovieById($movieId);
        });
        return $this->json($movie);
    }

    #[Route('/movie/{movieId}/trailer', name: 'app_movie_videos')]
    public function getTrailerVideo(TmdbApiService $tmdbService, CacheService $cacheService, int $movieId): Response
    {
        $videos = $cacheService->getItemsFromCache('tmdb_movie_trailer' . $movieId, function() use ($tmdbService, $movieId) {
            return $tmdbService->fetchTrailerByMovieId($movieId);
        });
        return $this->json($videos);
    }

    #[Route('/movie/topRatedMovies', name: 'app_top_rated_movie')]
    public function getTopRatedMovies(TmdbApiService $tmdbService, CacheService $cacheService): Response
    {
        $movies = $cacheService->getItemsFromCache('tmdb_top_rated_movies', function() use ($tmdbService) {
            return $tmdbService->fetchTopRatedMovies();
        });
        return $this->json($movies);
    }

    #[Route('/rateMovie/{movieId}/{rating}', name: 'app_rate_movie')]
    public function AddRating(TmdbApiService $tmdbService, int $movieId, int $rating): Response
    {
        $movie = $tmdbService->postRatingMovie($movieId, $rating);
        $movie['rating'] = $rating;
        return $this->json($movie);
    }
}
