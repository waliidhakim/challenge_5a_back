<?php

namespace App\Controller;

use App\Entity\Prestataire;
use App\Repository\EstablishmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetEstablishmentsByPrestataireController extends AbstractController
{


    public function __invoke(Prestataire $prestataire, EstablishmentRepository $establishmentRepository): Response
    {
        $establishments = $establishmentRepository->findBy(['relateTo' => $prestataire]);

        // Transformer les données en format JSON (ou autre format nécessaire)
        // Retourner une réponse HTTP
    }
}
