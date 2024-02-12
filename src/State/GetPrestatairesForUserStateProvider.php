<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use App\Repository\EstablishmentRepository;
use App\Repository\PrestataireRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

readonly class GetPrestatairesForUserStateProvider implements ProviderInterface
{
    public function __construct(private Security $security, private PrestataireRepository $prestataireRepository)
    {
    }
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /* @var User $user*/
        $user = $this->security->getUser();

        if(!$user)
        {
            throw new UnauthorizedHttpException("",'User does not existe');
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return $this->prestataireRepository->findAll();
        }

        if ($this->security->isGranted('ROLE_PRESTATAIRE')) {
            // Récupérer les établissements en fonction du prestataire associé à l'utilisateur
            return $user->getPrestataires();
        }



        return [];
    }
}
