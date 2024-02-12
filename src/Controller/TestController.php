<?php

namespace App\Controller;

//use App\Services\MailerService;
use App\Entity\Test;
use App\Form\TestType;
use App\Services\UploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TestController extends AbstractController
{

    #[Route('/test', name: 'app_test')]
    #[IsGranted('PUBLIC_ACCESS')]
    public function index(ParameterBagInterface $params): Response
    {

        // Ou
        // $frontUrl = getenv('FRONT_URL') ?: 'Valeur par défaut';
        $izan  = "izan";
        dd("izzzancc");

        //echo xdebug_info();
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    #[Route('/email/send', name: 'app.email.send')]
    public function someAction(MailerInterface $mailerService)
    {   
        
        $variable0 = "jfdklfd";
        $variable1 = "jfdklfd";
        $variable2 = "jfdklfd";
        $email = (new TemplatedEmail())
            ->from('idirwalidhakim31@gmail.com')
            ->to('idirwalidhakim32@gmail.com')
            ->subject('Subject')
            ->htmlTemplate('test/emailTest.html.twig')
            ->context(['variable0' => $variable0 , 'variable1' =>  $variable1, 'variable2' => $variable2 ]);

        try {
            $mailerService->send($email);
        } catch (TransportExceptionInterface $e) {
            return $e->getMessage();
        }

        return $this->render('test/index.html.twig', [
            'controller_name' => 'EmailController',
        ]);
    }


    #[Route('/file/upload', name: 'app.file.upload')]
    public function uploadFile(UploaderService $uploaderService, Request $request, EntityManagerInterface $manager)
    {

        $test = new Test();
        $form = $this->createForm(TestType::class, $test);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $test = $form->getData();

            $photo = $form->get('image')->getData();
            if ($photo) {

                $uploadsDirectory =  $this->getParameter('uploadsDirectory');
//                $directory =  $uploadsDirectory . "/personnes";
                $newFilename = $uploaderService->uploadFile($photo, "users");
                //dd($newFilename);
                $test->setImage($newFilename);
            }

            $manager->persist($test);
            $manager->flush();
            $this->addFlash('success',"Profile updated successfully");
            return $this->redirectToRoute('app_test');
        }


        return $this->render('test/formTest.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
