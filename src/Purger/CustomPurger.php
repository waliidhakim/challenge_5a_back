<?php

//namespace App\Purger;
//
//
//use Doctrine\Common\DataFixtures\Purger\ORMPurgerInterface;
//use Doctrine\DBAL\Exception;
//use Doctrine\ORM\EntityManagerInterface;
//
//class CustomPurger implements ORMPurgerInterface
//{
//    public function __construct(private readonly EntityManagerInterface $entityManager)
//    {
//
//    }
//
//    public function purge() : void
//    {
//        $this->setEntityManager($this->entityManager);
//        $conn = $this->entityManager->getConnection();
//
//        try {
//            $conn->beginTransaction();
//            $conn->executeQuery('DELETE FROM establishment');
//            $conn->executeQuery('DELETE  FROM prestataire');
//            $conn->executeQuery('DELETE  FROM "user"');
//        } catch (Exception $e) {
//            $conn->rollBack(); // Annuler la transaction en cas d'erreur
//            throw $e;
//        }
//
//
//    }
//
//    public function setEntityManager(EntityManagerInterface $em)
//    {
//
//    }
//}


namespace App\Purger;

use App\Repository\CategoryRepository;
use App\Repository\EstablishmentRepository;
use App\Repository\PrestataireRepository;
use App\Repository\PrestationRepository;
use Doctrine\Common\DataFixtures\Purger\ORMPurgerInterface;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;

class CustomPurger implements ORMPurgerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager,
                                private readonly EstablishmentRepository $establishmentRepository,
                                private readonly PrestataireRepository $prestataireRepository,
                                private  readonly  PrestationRepository $prestationRepository,
                                private readonly CategoryRepository $categoryRepository

    )
    {
        $this->entityManager = $entityManager;
    }

    #[NoReturn] public function purge(): void
    {

        $this->removeEtabs();
        $this->removePrestas();
        $this->removePrestations();

        //$cats = $this->categoryRepository->findAll();
        //dd($cats);
        $this->removeCategories();



        //dd($etabs);
        $conn = $this->entityManager->getConnection();
        //dd("hello");

        try {
            $conn->beginTransaction();

            $conn->executeQuery('DELETE FROM "user"');
            $conn->executeQuery('DELETE FROM prestataire');
            $conn->executeQuery('DELETE FROM category');
            $conn->executeQuery('DELETE FROM establishment');
            $conn->executeQuery('DELETE FROM prestation');

            $conn->commit(); // Valider la transaction

        } catch (\Exception $e) {

            $conn->rollBack(); // Annuler la transaction en cas d'erreur
            throw $e;
        }
    }

    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    private function removeEtabs(): void
    {
        $etabs = $this->establishmentRepository->findAll();


        foreach($etabs as $etab)
        {
            $etab->setManager(null);
            $etab->setRelateTo(null);
            $this->entityManager->persist($etab);

        }
        $this->entityManager->flush();
//        foreach($etabs as $etab)
//        {
//            $this->entityManager->remove($etab);
//        }
        $this->entityManager->clear();
    }

    private function  removePrestas() : void
    {
        $prestas = $this->prestataireRepository->findAll();
        foreach($prestas as $presta)
        {
            $presta->setOwner(null);
            $this->entityManager->persist($presta);

        }
        $this->entityManager->flush();
//        foreach($prestas as $presta)
//        {
//
//            $this->entityManager->remove($presta);
//
//        }
        $this->entityManager->clear();
    }

    private function removePrestations(): void
    {
        $prestations = $this->prestationRepository->findAll();

        foreach($prestations as $prestation) {
            $this->entityManager->remove($prestation);
        }
    }
    private function removeCategories(): void
    {
        $categories = $this->categoryRepository->findAll();

        foreach ($categories as $category) {
            foreach ($category->getPrestations() as $prestation) {
                $prestation->setCategory(null);
                $this->entityManager->persist($prestation);
            }
            $this->entityManager->flush();

            $this->entityManager->remove($category);
        }
        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}

