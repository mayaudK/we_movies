<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AutocompleteController extends AbstractController
{
    #[Route('/autocomplete', name: 'app_autocomplete')]
    public function index(): Response
    {
        return $this->render('autocomplete/index.html.twig', [
            'controller_name' => 'AutocompleteController',
        ]);
    }
}
