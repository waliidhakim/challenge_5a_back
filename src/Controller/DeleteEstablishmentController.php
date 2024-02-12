<?php

namespace App\Controller;

use App\Entity\Establishment;
use App\Entity\Prestataire;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DeleteEstablishmentController extends AbstractController
{
    public function __construct(private Security $security)
    {
    }

    public function __invoke(Establishment $data, EntityManagerInterface $em): Response|UnauthorizedHttpException
    {
        if($this->security->isGranted('ROLE_ADMIN')){
            $this->process($data, $em);
            return new Response(null, Response::HTTP_NO_CONTENT);
        }
        /* @var User $user*/
        $user = $this->security->getUser();
        if($this->security->isGranted('ROLE_PRESTATAIRE') && $user->getId() == $data->getRelateTo()->getOwner()->getId()){
            $this->process($data, $em);
            return new Response(null, Response::HTTP_NO_CONTENT);
        }


        return new UnauthorizedHttpException("","You don't have permission to perform this action");


        // Réponse après suppression

    }


    private function process(Establishment $etabToRemove,EntityManagerInterface $em ) : void
    {
        foreach ($etabToRemove->getEmployees() as $employee) {
            $employee->setEstablishment(null);
            $em->persist($employee);
        }

        $etabToRemove->setManager(null);


        // Supprimer le Prestataire
        $em->remove($etabToRemove);
        $em->flush();
    }
}
