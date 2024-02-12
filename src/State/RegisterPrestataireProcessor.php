<?php

// src/State/RegisterPrestataireProcessor.php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Prestataire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Bundle\SecurityBundle\Security;


class RegisterPrestataireProcessor implements ProcessorInterface {
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MailerInterface $mailer,
        private Security $security
    ) {}

    public function process($data, Operation $operation, array $uriVariables = [], array $context = []) {

        $prestataire = new Prestataire();
        $prestataire = $data;

        $prestataire->setStatus('waiting for approval');

        $potentielOwner = $this->security->getUser();
        if (!$potentielOwner) {
            throw new UnauthorizedHttpException("",'User not authenticated');
        }

        $prestataire->setOwner($potentielOwner);

        $email = (new TemplatedEmail())
            ->from($prestataire->getContactInfos())
            ->to('idirwalidhakim32@gmail.com')
            ->subject('Subject')
            ->htmlTemplate('prestataire_registration.html.twig')
            ->context([
                        'name' => $data->getName() ,
                        'kbis' => $data->getKbis() ,
                        'description' => $data->getDescription(),
                        'firstname' => $potentielOwner->getFirstname(),
                        'lastname' => $potentielOwner->getLastname(),
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            return $e->getMessage();
        }

        $this->entityManager->persist($prestataire);
        $this->entityManager->flush();

        return $prestataire;
    }
}
