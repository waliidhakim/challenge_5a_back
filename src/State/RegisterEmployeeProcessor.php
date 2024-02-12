<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Establishment;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterEmployeeProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private Security $security,
        private UserRepository $userRepository
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $dto = $data; // $data est une instance de RegisterEmployeeDto
        /* @var User $currentUser*/
        $currentUser = $this->security->getUser();
        $establishment = $this->entityManager->getRepository(Establishment::class)->find($dto->establishmentId);


        if (!$establishment) {
            throw new NotFoundHttpException("Establishement not found");
        }

        if($this->userRepository->findOneBy(['email'=>$dto->email]))
        {
            throw new HttpException( 409,"User with this email adress already exist");
        }

        if (
            $currentUser == $establishment->getManager()  ||
            $establishment->getRelateTo()->getOwner() == $currentUser)
        {
            $user = new User();
            $user->setEmail($dto->email);
            $user->setPassword($this->passwordHasher->hashPassword($user, $dto->password));
            $user->setFirstname($dto->firstname);
            $user->setLastname($dto->lastname);
            $user->setRoles(['ROLE_EMPLOYE']);
            $user->setEstablishment($establishment); // Associer l'employé à l'établissement

            $this->entityManager->persist($user);
            $this->entityManager->flush();


            return $user;
        }

        throw new UnauthorizedHttpException("","Not the actual Prestataire Owner or manager");


    }
}
