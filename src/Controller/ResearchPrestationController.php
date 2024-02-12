<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PrestationRepository;

class ResearchPrestationController extends AbstractController
{
    #[Route('/api/prestations/research', name: 'research_prestations', methods: ['POST'])]
    public function __invoke(Request $request, PrestationRepository $prestationRepository): Response
    {
        $data = json_decode($request->getContent(), true);

        // Exemple de récupération de critères (ajustez selon vos besoins)
        $name = $data['name'] ?? '';
        $category = $data['category'] ?? '';
        $priceRange = $data['price'] ?? '';

        $results = $prestationRepository->findBy(['name'=>$name]);

        return $this->json($results);
    }
}