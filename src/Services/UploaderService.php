<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Aws\S3\S3Client;   // cette dépendance est à installer !

class UploaderService
{

//    private $env;
    private $bucketName = 'challange-esgi';    // nom de ton bucket que tu devras créer sur aws

    public function __construct(
        private SluggerInterface $slugger,
        private string $uploadsDirectory,
        private S3Client $s3,
        private string $env
    )
    {

//        $this->env = $env;
    }

    public function uploadFile(UploadedFile $file, string $directoryFolder)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        //enregistrer toujours dans le bucket s3
        if (1) {
//        if (1) {
            // S3 upload for production


            $bucketPath = $directoryFolder.'/'. $newFilename;
            //dd($bucketPath);
            try {
                //dd($bucketPath);
                $result = $this->s3->putObject([
                    'Bucket' => $this->bucketName,
                    'Key' => $bucketPath,
                    'Body'   => fopen($file->getPathname(), 'rb'),
//                    'ACL'    => 'public-read'  // optional
                ]);

                if (!$result['ObjectURL']) {
                    throw new FileException('File could not be uploaded to S3.');
                }
            } catch (\Exception $e) {

                throw $e;  // Re-throw the exception to see it clearly
            }
        }

//        else {
//            // Local storage for dev
//            try {
//                // dd($this->uploadsDirectory . "/" . $directoryFolder);
//
//                $file->move(
//                    $this->uploadsDirectory . "/" .  $directoryFolder,
//                    $newFilename
//                );
//            } catch (FileException $e) {
//                // handle local storage exceptions
////                dd($e);
//                throw $e;
//            }
//        }

        return $newFilename;
    }
}
