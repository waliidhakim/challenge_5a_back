<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;


class DeteteBookingController extends AbstractController
{
    public function __construct(private Security $security)
    {
    }

    public function __invoke(Booking $data, EntityManagerInterface $em): Response|UnauthorizedHttpException
    {
        if($this->security->isGranted('ROLE_ADMIN')){
            $this->process($data, $em);
            return new Response(null, Response::HTTP_NO_CONTENT);
        }
        /* @var User $user*/
        $user = $this->security->getUser();
        if($this->security->isGranted('ROLE_USER') && $user->getId() == $data->getBookedBy()->getId()){
            $this->process($data, $em);
            return new Response(null, Response::HTTP_NO_CONTENT);
        }


        return new UnauthorizedHttpException("","You don't have permission to perform this action");


        // Réponse après suppression

    }


    private function process(Booking $bookingToRemove,EntityManagerInterface $em ) : void
    {
        $bookingToRemove->setBookedBy(null);
        $bookingToRemove->setPrestation(null);

        $em->remove($bookingToRemove);
        $em->flush();
    }
}
