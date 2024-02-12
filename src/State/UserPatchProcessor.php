<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Services\UploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class UserPatchProcessor implements ProcessorInterface
{


    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UploaderService $fileUploader,
        private readonly RequestStack $requestStack
    ) {


    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $request = $this->requestStack->getCurrentRequest();

        
        // Gérer le téléchargement d'image

        $firstname = $request->request->get('firstname');
        $lastname = $request->request->get('lastname');
        if (!$firstname || !$lastname) {
            throw new BadRequestHttpException('Firstname or  lastname must be provided.');
        }
        $file = $request->files->get('image');
        if ($file) {
            $file = $request->files->get('image');
            $directoryFolder = 'user_images'; // Le dossier dans lequel vous voulez sauvegarder l'image
            $filename = "";
            try {
                $filename = $this->fileUploader->uploadFile($file, $directoryFolder);
            } catch (\Exception $e) {

            }
            $data->setImage($filename); // Assurez-vous que votre entité User a un setter pour l'image
        }

        // Gérer également les autres champs de mise à jour ici

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }
}