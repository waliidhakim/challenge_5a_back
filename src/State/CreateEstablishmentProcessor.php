<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Establishment;
use App\Entity\Prestataire;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Mailer\MailerInterface;

class CreateEstablishmentProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private  readonly Security               $security
    )
    {

    }
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        // Handle the state
        $etablissement = new Establishment();
        $etablissement = $data;

        $prestataireNameToUpdate = $data->getRelateTo()->getName();

        /* @var User $owner */
        $owner = $this->security->getUser();
        if (!$owner) {
            throw new UnauthorizedHttpException("",'User does not existe');
        }

        $prestataireToBeUpdated = null ;
        foreach ($owner->getPrestataires() as $prestataire) {
            if($prestataire->getName() == $prestataireNameToUpdate)
            {
                $prestataireToBeUpdated = $prestataire;
            }
        }


        if(!$prestataireToBeUpdated){
            throw new UnauthorizedHttpException("",'Prestataire does not existe');
        }

        $prestataireToBeUpdated->addEstablishment($etablissement);

        $this->entityManager->persist($prestataireToBeUpdated);
        $this->entityManager->persist($etablissement);
        $this->entityManager->flush();

        return $etablissement;


    }
}
