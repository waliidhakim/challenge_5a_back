<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Prestataire;
use App\Entity\User;
use App\Repository\PrestataireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Mailer\MailerInterface;

readonly class GetEstablishmentByPrestataireStateProvider implements ProviderInterface
{
    public function __construct(
        private PrestataireRepository $prestataireRepo,
        private Security              $security,
    ) {}
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
//        /* @var User $user */
//        $user = $this->security->getUser();
//        if (!$user) {
//            throw new UnauthorizedHttpException('User does not existe');
//        }
//
//        /* @var User $owner */
////        $owner = "";
////        if (!$user) {
////            throw new UnauthorizedHttpException('User does not existe');
////        }
//
//        $prestataire =  $this->prestataireRepo->findOneBy(['id'=>$uriVariables['id']]);
//
//        //check weather user is the owner of the presta
//        if($owner != $prestataire->getOwner())
//        {
//            throw new UnauthorizedHttpException('User does not the owner of the prestation');
//        }
//
//        return $prestataire->getEstablishments();


        // Récupération de l'utilisateur connecté
        /** @var User $user */
        $user = $this->security->getUser();
        if (!$user) {
            throw new UnauthorizedHttpException('Bearer', 'User does not exist');
        }

        // Récupération du prestataire en fonction de l'ID dans l'URI
        /** @var Prestataire $prestataire */
        $prestataire = $this->prestataireRepo->find($uriVariables['id']);
        if (!$prestataire) {
            throw new UnauthorizedHttpException('Bearer', 'Prestataire does not exist');
        }

        // Vérification si l'utilisateur connecté est le propriétaire du prestataire
        if ($user !== $prestataire->getOwner()) {
            throw new UnauthorizedHttpException('Bearer', 'You are not the owner of this prestataire');
        }

        // Retourner les établissements du prestataire
        return $prestataire->getEstablishments();

    }
}
