<?php


namespace App\Controller;

use App\Entity\Media;
use App\Entity\User;
use App\Entity\Video;
use App\Services\UploaderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class MediaOploaderController extends AbstractController
{
    public function __construct(private readonly UploaderService $uploaderService)
    {
    }

    public function __invoke(Request $request, User $user): User
    {
        //dd($request->files->get('file'));
        $firstname = $request->request->get('firstname');
        $lastname = $request->request->get('lastname');
        $media = new Media();
        $media->setTitle("nimp");
        $media->setFile($request->request->get('image'));

        $fileToSave = $request->files->get('image');
        $newFilename = $this->uploaderService->uploadFile($fileToSave, "users");

        $user->setImage("https://challange-esgi.s3.eu-central-1.amazonaws.com/users/". $newFilename);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);

        $media->setUpdatedAt(new \DateTimeImmutable());

        return $user;
    }
}