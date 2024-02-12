<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Establishment;
use App\Entity\User;
use App\Repository\EstablishmentRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

readonly class GetOneEstablishmentStateProvider implements ProviderInterface
{
    public function __construct(
        private Security                $security,
        private EstablishmentRepository $establishmentRepository)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Establishment
    {
        $establisement = $this->establishmentRepository->findOneBy(['id' => $uriVariables['id']]);

        if(!$establisement){
            throw new NotFoundHttpException("Not found. Establisement does not exist");
        }
        /* @var User $user*/
        $user = $this->security->getUser();

        if(!$user)
        {
            throw new NotFoundHttpException("Not found. User does not exist");
        }



        $id = $uriVariables['id'];
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return $establisement ;
        }



        if ($this->security->isGranted('ROLE_PRESTATAIRE')) {
            // Récupérer les établissements en fonction du prestataire associé à l'utilisateur
            $userPrestaOwner =  $establisement->getRelateTo()->getOwner();

            if($userPrestaOwner != $user){
                throw new UnauthorizedHttpException("", "Unautorized. User not the prestatire Owner");
            }
            return $establisement;
        }

        if ($this->security->isGranted('ROLE_MANAGER')) {
            // Récupérer les établissements en fonction du prestataire associé à l'utilisateur
            $etabManager = $establisement->getManager();

            if(!$etabManager){
                throw new NotFoundHttpException("Not found. Manger does not exist");
            }
            $condition = $etabManager != $user ;
            if($etabManager->getId() != $user->getId()){
                throw new NotFoundHttpException("Not found. Manger does not exist");

//                throw new UnauthorizedHttpException("", "Unautorized. User not the manager");
            }

            return $establisement;
        }


        return [];
    }
}
