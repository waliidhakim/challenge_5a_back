<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Establishment;
use App\Entity\User;
use App\Repository\EstablishmentRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

readonly class AssignManagerToEstablishmentStateProcessor implements ProcessorInterface
{
    public function __construct(private Security                $security,
                                private EstablishmentRepository $establishmentRepository,
                                private UserRepository          $userRepository,
                                private EntityManagerInterface  $manager
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        //check if the user who owns the presta can update the establisement with the id in the url !!!!!

        /* @var User $user */
        $user = $this->security->getUser();

        //check if user exists
        if(!$user)
        {
            throw new UnauthorizedHttpException("", 'User does not exist');
        }


        $establishmentToUpdate = $this->establishmentRepository->findOneBy(['id'=> $uriVariables['id']]);

        $userToBeManager = $this->userRepository->findOneBy(['email'=>$data->getEmail()]);

        if(!$userToBeManager)
        {
            throw new NotFoundHttpException('User does not exist');
        }


        if( ! in_array('ROLE_MANAGER' ,$userToBeManager->getRoles()) )
        {
            throw new NotFoundHttpException('User not a manager');
        }

        $userToBeManager->setRoles(['ROLE_MANAGER']);
        $establishmentToUpdate->setManager($userToBeManager);

        $this->manager->flush();


        return $establishmentToUpdate;

    }
}
