<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use App\Repository\BookingRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class GetBookingsStateProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
        private BookingRepository $bookingRepository

    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Récupération de l'utilisateur connecté
        /** @var User $user */
        $user = $this->security->getUser();
        if (!$user) {
            throw new UnauthorizedHttpException('Bearer', 'User does not exist');
        }

        if($this->security->isGranted("ROLE_ADMIN")){
            return $this->bookingRepository->findAll();
        }
        if($this->security->isGranted("ROLE_USER")){
            return $user->getBookings();
        }
        return [];
    }
}
