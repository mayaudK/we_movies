<?php

namespace App\Controller;

use App\Service\CacheService;
use App\Service\TmdbApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TmdbController extends AbstractController
{
    #[Route('/tmdb', name: 'app_tmdb')]
    public function index(TmdbApiService $tmdbService, CacheService $cacheService): Response
    {
        $cacheKey = 'tmdb_' . md5('genres');
        $genres = $cacheService->getGenresFromCache($cacheKey, [$tmdbService, 'fetchGenresFromApi']);

        return $this->render('tmdb/index.html.twig', [
            'controller_name' => 'TmdbController',
            'genres' => $genres,
        ]);
    }
}
