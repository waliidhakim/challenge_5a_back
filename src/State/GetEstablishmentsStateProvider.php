<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use App\Repository\EstablishmentRepository;
use Symfony\Bundle\SecurityBundle\Security;
class GetEstablishmentsStateProvider implements ProviderInterface
{

    public function __construct(private readonly EstablishmentRepository $establishmentRepository, private readonly Security $security)
    {
    }
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /* @var User $user*/
        $user = $this->security->getUser();

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return $this->establishmentRepository->findAll();
        }

        if ($this->security->isGranted('ROLE_PRESTATAIRE')) {
            // Récupérer les établissements en fonction du prestataire associé à l'utilisateur
            return $this->establishmentRepository->findByPrestataireOwner($user);
        }



        return [];
    }
}
