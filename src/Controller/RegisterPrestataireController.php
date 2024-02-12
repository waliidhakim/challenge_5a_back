<?php


namespace App\Controller;

use App\Entity\Media;
use App\Entity\Prestataire;
use App\Entity\User;
use App\Services\UploaderService;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception\RuntimeException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class RegisterPrestataireController extends AbstractController
{
    public function __construct(
        private readonly UploaderService $uploaderService,
        private readonly EntityManagerInterface $entityManager,
        private readonly MailerInterface $mailer,
        private readonly Security $security
    )
    {
    }

    public function __invoke(Request $request): Prestataire
    {
        //dd($request->files->get('file'));
        /**  @var User $potentielOwner */
        $potentielOwner = $this->security->getUser();
        if (!$potentielOwner) {
            throw new UnauthorizedHttpException("",'User not authenticated');
        }

        $potentialPrestataire = new Prestataire();

        $potentialPrestataire->setOwner($potentielOwner);

        $potentialPrestataire->setName($request->request->get('name'));
        $potentialPrestataire->setAddress($request->request->get('address'));
        $potentialPrestataire->setDescription($request->request->get('description'));
        $potentialPrestataire->setContactInfos($request->request->get('contactInfos'));
        $potentialPrestataire->setKbis($request->request->get('kbis'));
        $potentialPrestataire->setSector($request->request->get('sector'));

        $potentialPrestataire->setStatus('waiting for approval');

        $media = new Media();
        $media->setTitle("nimp");
        $media->setFile($request->request->get('image'));

        $fileToSave = $request->files->get('image');
        try {
            $newFilename = $this->uploaderService->uploadFile($fileToSave, "prestas");
            $potentialPrestataire->setImage("https://challange-esgi.s3.eu-central-1.amazonaws.com/prestas/". $newFilename);
        } catch (\Exception $e) {
            throw new RuntimeException("Error saving S3 file");
        }

        $email = (new TemplatedEmail())
            ->from($potentialPrestataire->getContactInfos())
            ->to('idirwalidhakim32@gmail.com')
            ->subject('Subject')
            ->htmlTemplate('prestataire_registration.html.twig')
            ->context([
                'name' => $potentialPrestataire->getName() ,
                'kbis' => $potentialPrestataire->getKbis() ,
                'description' => $potentialPrestataire->getDescription(),
                'firstname' => $potentielOwner->getFirstname(),
                'lastname' => $potentielOwner->getLastname(),
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new RuntimeException("Error while sending the aprobation mail");
        }

        $this->entityManager->persist($potentialPrestataire);
        $this->entityManager->flush();

        //$user->setImage("https://challange-esgi.s3.eu-central-1.amazonaws.com/users/". $newFilename);


        $media->setUpdatedAt(new \DateTimeImmutable());

        return $potentialPrestataire;
    }
}