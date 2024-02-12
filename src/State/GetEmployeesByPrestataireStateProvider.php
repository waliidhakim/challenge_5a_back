<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Prestataire;
use App\Entity\User;
use App\Repository\PrestataireRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Mailer\MailerInterface;

readonly class GetEmployeesByPrestataireStateProvider implements ProviderInterface
{
    public function __construct(
        private PrestataireRepository $prestataireRepo,
        private Security              $security,
        private UserRepository $userRepo
    ) {}
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Récupération de l'utilisateur connecté
        /** @var User $user */
        $user = $this->security->getUser();
        if (!$user) {
            throw new UnauthorizedHttpException('Bearer', 'User does not exist');
        }

        if($this->security->isGranted("ROLE_ADMIN")){
            return $this->userRepo->findByRole("ROLE_EMPLOYEE");
        }

        if($this->security->isGranted("ROLE_PRESTATAIRE")){
            $userPrestataires = $user->getPrestataires();
            $allRelatedEtabs = [];
            $employeesForUserPresta = [];

            foreach ($userPrestataires as $prestataire) {
                $etabsForCurrentPresta = $prestataire->getEstablishments();

                // Vérifiez si l'établissement a des employés et les collecter
                foreach ($etabsForCurrentPresta as $establishment) {
                    $employees = $establishment->getEmployees(); // Assurez-vous que la méthode getEmployees existe et retourne les employés de l'établissement
                    $etabManager = $establishment->getManager();
                    //ajouter le manager à la liste si il existe
                    if($etabManager){
                        $employeesForUserPresta[] = $etabManager;
                    }
                    // Si l'établissement a des employés, les ajouter à la liste des employés
                    if ($employees) {
                        foreach ($employees as $employee) {
                            $employeesForUserPresta[] = $employee;
                        }
                    }
                }
            }
            return $employeesForUserPresta;
        }



        return [];
    }

    public function getEmployees(User $userPrestataire){

    }
}
