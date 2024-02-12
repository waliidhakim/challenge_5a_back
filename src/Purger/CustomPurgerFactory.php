<?php

namespace App\Purger;
// ...
use App\Repository\CategoryRepository;
use App\Repository\EstablishmentRepository;
use App\Repository\PrestataireRepository;
use App\Repository\PrestationRepository;
use Doctrine\Bundle\FixturesBundle\Purger\PurgerFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\DataFixtures\Purger\PurgerInterface;
readonly class CustomPurgerFactory implements PurgerFactory
{



    public function __construct(
        private EstablishmentRepository $establishmentRepository ,
        private PrestataireRepository   $prestataireRepository,
        private PrestationRepository    $prestationRepository,
        private CategoryRepository      $categoryRepository
    )
    {

    }
    public function createForEntityManager(?string $emName, EntityManagerInterface $em, array $excluded = [], bool $purgeWithTruncate = false) : PurgerInterface
    {
//        return new CustomPurger($em);
        return new CustomPurger(
            $em,
            $this->establishmentRepository,
            $this->prestataireRepository,
            $this->prestationRepository,
            $this->categoryRepository
        );
    }


}