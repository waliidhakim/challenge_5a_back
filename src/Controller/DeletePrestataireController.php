<?php

namespace App\Controller;

use App\Entity\Prestataire;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DeletePrestataireController extends AbstractController
{
    public function __construct(private Security $security)
    {
    }

    public function __invoke(Prestataire $data, EntityManagerInterface $em): Response|UnauthorizedHttpException
    {
        if($this->security->isGranted('ROLE_ADMIN')){
            $this->process($data, $em);
            return new Response(null, Response::HTTP_NO_CONTENT);
        }
        /* @var User $user*/
        $user = $this->security->getUser();
        if($this->security->isGranted('ROLE_PRESTATAIRE') && $user->getId() == $data->getOwner()->getId()){
            $this->process($data, $em);
            return new Response(null, Response::HTTP_NO_CONTENT);
        }


        return new UnauthorizedHttpException("","You don't have permission to perform this action");


        // Réponse après suppression

    }


    private function process(Prestataire $prestataireToRemove,EntityManagerInterface $em ) : void
    {
        foreach ($prestataireToRemove->getEstablishments() as $establishment) {
            $establishment->setRelateTo(null);
            $em->persist($establishment);
        }

        $prestataireToRemove->setOwner(null);
        // Supprimer le Prestataire
        $em->remove($prestataireToRemove);
        $em->flush();
    }
}
