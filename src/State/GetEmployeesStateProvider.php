<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use App\Repository\PrestataireRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use function Symfony\Component\String\s;

class GetEmployeesStateProvider implements ProviderInterface
{
    private Collection $employees;
    public function __construct(
        private readonly Security $security,
        private readonly PrestataireRepository $prestataireRepository,
        private readonly UserRepository $userRepository,
    )
    {
        $this->employees = new ArrayCollection();
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {

        /* @var User $user */
        $user = $this->security->getUser();

        //check if user exists
        if(!$user)
        {
            throw new NotFoundHttpException('User does not exist');
        }

        if(in_array('ROLE_ADMIN' ,$user->getRoles())){
            $userPrestataires = $this->prestataireRepository->findAll();
            $this->getEmployees($userPrestataires);

            return $this->employees;
        }

        if( !in_array('ROLE_PRESTATAIRE' ,$user->getRoles()) )
        {
            throw new NotFoundHttpException('User not the prestataire owner ');
        }

        else {
            $userPrestataires = $user->getPrestataires();
            $this->getEmployees($userPrestataires);
            return $this->employees;
        }






        return $this->employees;
    }

    public function getEmployees( $prestataires)
    {
        foreach ($prestataires as $prestataire)
        {
            foreach ( $prestataire->getEstablishments() as $establishment )
            {
                if($establishment->getManager())
                    $this->employees->add($establishment->getManager());

                if($establishment->getEmployees())
                {
                    foreach ($establishment->getEmployees() as $employee ){
                        $this->employees->add($employee);
                    }
                }

            }
        }
    }
}
