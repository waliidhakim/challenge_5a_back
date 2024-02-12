<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'app_registration')]
    public function index(): Response
    {
        return $this->render('registration/index.html.twig', [
            'controller_name' => 'RegistrationController',
        ]);
    }

//    #[Route('/register', name: 'app_register', methods: 'POST')]
//    public function register(Request $request, UserPasswordHasherInterface  $passwordHasher , EntityManagerInterface $manager): Response
//    {
//        $userRepository = $manager->getRepository(User::class);
//        $requestBody = $request->getPayload()->all();
//
//        if($userRepository->findOneBy(['email' => $requestBody['email']]) )
//        {
//            return $this->json([
//                'status' => 'error',
//                'message' => 'User already exists'
//            ]);
//        }
//
//        $user = new User();
//        $user->setEmail($requestBody['email']);
//
//        $hashedPassword = $passwordHasher->hashPassword(
//            $user,
//            $requestBody['password']
//        );
//        $user->setPassword($hashedPassword);
//        $user->setFirstname($requestBody['firstname']);
//        $user->setLastname($requestBody['lastname']);
//
//
//        $manager->persist($user);
//        $manager->flush();
//
//
//        return $this->json([
//            'status' => 'success',
//            'data' => $user
//        ]);
//    }
}
