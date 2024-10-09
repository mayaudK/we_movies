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
        $movies = $cacheService->getItemsFromCache('tmdb_movies_' . $genre, function() use ($tmdbService, $genre) {
            return $tmdbService->fetchMoviesByGenre($genre);
        });
        return $this->json($movies);
    }
}
