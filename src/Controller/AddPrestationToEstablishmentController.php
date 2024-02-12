<?php


namespace App\Controller;

use App\Entity\Category;
use App\Entity\Establishment;
use App\Entity\Media;
use App\Entity\Prestataire;
use App\Entity\Prestation;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Services\UploaderService;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception\RuntimeException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class AddPrestationToEstablishmentController  extends AbstractController
{
    public function __construct(
        private readonly UploaderService $uploaderService,
        private readonly EntityManagerInterface $entityManager,
//        private readonly MailerInterface $mailer,
        private readonly Security $security,
        private readonly CategoryRepository $categoryRepository
    )
    {
    }

    public function __invoke(Request $request, Establishment $establishment): Establishment
    {
        /** @var User $user */
        $user = $this->security->getUser();
        if (!$user) {
            throw new UnauthorizedHttpException("", 'User not authenticated');
        }

        // Admin peut effectuer l'opération directement
        if ($this->security->isGranted('ROLE_ADMIN')) {
            // Logique pour traiter la requête pour l'admin
            return $this->processRequest($request, $establishment);
        }

        // Vérification pour le rôle de prestataire
        if ($this->security->isGranted('ROLE_PRESTATAIRE')) {
            $userPrestataireOwner = $establishment->getRelateTo()->getOwner();
            if ($user !== $userPrestataireOwner) {
                throw new UnauthorizedHttpException("", 'User prestataire not the owner of the establishment');
            }
            // Logique pour traiter la requête pour le prestataire
            return $this->processRequest($request, $establishment);
        }

        // Vérification pour le rôle de manager
        if ($this->security->isGranted('ROLE_MANAGER')) {
            $establishmentManager = $establishment->getManager();
            if ($user !== $establishmentManager) {
                throw new UnauthorizedHttpException("", 'User not the manager of the establishment');
            }
            // Logique pour traiter la requête pour le manager
            return $this->processRequest($request, $establishment);
        }

        // Si aucun rôle n'est accordé
        throw new UnauthorizedHttpException("", 'User does not have permission to perform this action');
    }


    private function processRequest(Request $request, Establishment $establishment): Establishment
    {
        $newPrestation = new Prestation();

        $newPrestation->setName($request->request->get('name'));
        $newPrestation->setDescription($request->request->get('description'));
        $newPrestation->setPrice($request->request->get('price'));
        $newPrestation->setDuration($request->request->get('duration'));

        $categoryName = $request->request->get('category');

        $existingCategory = $this->categoryRepository->findOneBy(['name'=>$categoryName]);

        if(!$existingCategory)
        {
            $newCategory = new Category();
            $newCategory->setName($categoryName);
            $newPrestation->setCategory($newCategory);
            $this->entityManager->persist($newCategory);
            $this->entityManager->flush();
        }
        else {
            $newPrestation->setCategory($existingCategory);
        }

        $media = new Media();
        $media->setTitle("nimp");
        $media->setFile($request->request->get('image'));

        $fileToSave = $request->files->get('image');
        try {
            $newFilename = $this->uploaderService->uploadFile($fileToSave, "prestations");
            $newPrestation->setImage("https://challange-esgi.s3.eu-central-1.amazonaws.com/prestations/". $newFilename);
        } catch (\Exception $e) {
            throw new RuntimeException("Error saving S3 file");
        }


        $establishment->addPrestation($newPrestation);
        $this->entityManager->persist($newPrestation);
        $this->entityManager->flush();

        //$user->setImage("https://challange-esgi.s3.eu-central-1.amazonaws.com/users/". $newFilename);


        $media->setUpdatedAt(new \DateTimeImmutable());

        return $establishment;

    }
}