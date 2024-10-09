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
    #[Route('/tmdb', name: 'app_tmdb')]
    public function index(TmdbApiService $tmdbService, CacheService $cacheService): Response
    {
        $cacheGenreKey = 'tmdb_genres';
        $cacheFirstBestMovieKey = 'tmdb_first_best_movie';
        $genres = $cacheService->getItemsFromCache($cacheGenreKey, [$tmdbService, 'fetchGenresFromApi']);
        $firstBestMovie = $cacheService->getItemsFromCache($cacheFirstBestMovieKey, [$tmdbService, 'fetchFirstBestMovie']);
        return $this->render('tmdb/index.html.twig', [
            'first_best_movie' => $firstBestMovie,
            'genres' => $genres,
//            'movie_list' => $tmdbService->getMoviesByGenre($firstBestMovie['genre_id'])
        ]);
    }

    #[Route('/autocomplete', name: 'app_autocomplete')]
    public function autocomplete(Request $request, AutocompleteService $autocompleteService, CacheService $cacheService): Response
    {
        $query = $request->query->get('query', '');
        $results = $autocompleteService->getAutocompleteSuggestions($query);

        return $this->json($results);
    }
}
