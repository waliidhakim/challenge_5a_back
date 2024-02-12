<?php

namespace App\Controller;

use App\Entity\Video;
use App\Services\UploaderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class VideoOploaderController extends AbstractController
{
    public function __construct(private readonly UploaderService $uploaderService)
    {
    }

    public function __invoke(Request $request) : Video
    {
        //dd($request->files->get('file'));
        $video = new Video();
        $video->setTitle($request->request->get('title'));
        $video->setFile($request->request->get('file'));

        $fileToSave = $request->files->get('file');
        $newFilename = $this->uploaderService->uploadFile($fileToSave, "users");

        $video->setUpdatedAt(new \DateTimeImmutable());

        return $video;
    }
}