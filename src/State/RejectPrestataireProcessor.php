<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Prestataire;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;


class RejectPrestataireProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MailerInterface        $mailer,
        private Security               $security
    )
    {

    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $id_prestataire = $uriVariables['id'];


        $prestataire = $this->entityManager->getRepository(Prestataire::class)->findOneBy(['id' => $id_prestataire]);

        if(!$prestataire){
            throw new NotFoundHttpException('Prestataire not found');
        }

        $owner = $prestataire->getOwner();

        if(!$owner){
            throw new NotFoundHttpException('No owner found to the prestataire ');
        }

        $prestataire->setStatus('Rejected');
//        $owner->setRoles(['ROLE_PRESTATAIRE']);
        //$owner->removePrestataire($prestataire);

        $email = (new TemplatedEmail())
            ->from($prestataire->getContactInfos())
            ->to('idirwalidhakim32@gmail.com')
            ->subject('Prestataire membership rejected')
            ->htmlTemplate('prestataire_membership_rejected.html.twig')
            ->context([
                'name' => $prestataire->getName() ,
                'kbis' => $prestataire->getKbis() ,
                'description' => $prestataire->getDescription(),
                'firstname' => $owner->getFirstname(),
                'lastname' => $owner->getLastname(),
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            return $e->getMessage();
        }

        $this->entityManager->flush();


        return $prestataire;
    }
}
