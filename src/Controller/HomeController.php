<?php

namespace App\Controller;

use App\Services\StarWarsApiService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{

    public function __construct(private readonly StarWarsApiService  $starWarsApiService)
    {

    }

    #[Route('/personnage/{id}', name: 'app_personnage', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function personnage(int $id): Response
    {
        return $this->render('home/personnage.html.twig', [
            'personnage' => $this->starWarsApiService->getPersonnage($id),
        ]);
    }


    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {

        // Get all characters from the service
        $personnages = $this->starWarsApiService->getPersonnages();

        // Pagination settings
        $itemsPerPage = 8;
        $currentPage = $request->query->getInt('page', 1); // Get current page from query parameter (defaults to 1)
        $totalItems = count($personnages); // Total number of characters
        $totalPages = ceil($totalItems / $itemsPerPage); // Total pages

        // Calculate offset and limit to slice the characters array
        $offset = ($currentPage - 1) * $itemsPerPage;
        $paginatedPersonnages = array_slice($personnages, $offset, $itemsPerPage);

        return $this->render('home/index.html.twig', [
            'personnages' => $paginatedPersonnages, // Only send the paginated characters
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
        ]);
    }


}

