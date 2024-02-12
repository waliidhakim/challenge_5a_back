<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\PrestationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\PreAuthenticationJWTUserToken;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class PaymentController extends AbstractController
{
    public function __construct(
        private readonly Security $security,
        private PrestationRepository $prestationRepository,
        private EntityManagerInterface $manager,
        private JWTTokenManagerInterface $jwtManager,
        private UserRepository $userRepository,
        private JWTEncoderInterface $jwtEncoder

    )
    {
    }

//    #[Route('/create-checkout-session', methods: ['POST'])]
//    public function createCheckoutSession(Request $request): JsonResponse | UnauthorizedHttpException | NotFoundHttpException
//    {
//
//        $data = json_decode($request->getContent(), true);
//        Stripe::setApiKey('sk_test_51NVqQtKRXD3JBQTyC0Yk1Qh9fZEb9e9sfKMDqp7TdShloj5KMCbLkmnFNW7dCJn4Knzsfab67U1tNs0Jibk6NXa500wZvliJnx');
//
//        $user = $this->security->getUser();
//        //dd($user);
//        if(!$user)
//        {
//            return new UnauthorizedHttpException("", "You have to be logged in to perform this action");
//        }
//
//        $prestationToBeBaught = $this->prestationRepository->findOneBy(['id'=>$data['idPresta']]);
//        if(!$prestationToBeBaught)
//        {
//            return new NotFoundHttpException("Prestation not found");
//        }
//
//        $newBooking = new Booking();
//        $newBooking->setBookedBy($this->security->getUser());
//        $newBooking->setPrestation($prestationToBeBaught);
//
//        $this->manager->persist($newBooking);
//        $this->manager->flush();
//
//        $session = Session::create([
//            'payment_method_types' => ['card'],
//            'line_items' => [[
//                'price_data' => [
//                    'currency' => 'eur',
//                    'product_data' => [
//                        'name' => $data['productName'],
//                        'images' => [$data['imageUrl']],
//                    ],
//                    'unit_amount' => $data['price']*100, // Prix en centimes, par exemple 20,00 €
//
//                ],
//                'quantity' => 1,
//            ]],
//            'mode' => 'payment',
//            'success_url' => $_ENV["FRONT_URL"]."/reservations",
//            'cancel_url' => $_ENV["FRONT_URL"].'/payment/errorPayment',
//        ]);
//        //dd($request);
//        return $this->json(['id' => $session->id]);
//    }


    #[Route('/create-checkout-session', methods: ['POST'])]
    public function createCheckoutSession(Request $request, JWTTokenManagerInterface $JWTManager): JsonResponse | NotFoundHttpException
    {
        $authorizationHeader = $request->headers->get('Authorization');

        if (!$authorizationHeader) {
            throw new UnauthorizedHttpException("", "No authorization header provided");
        }
        $data = json_decode($request->getContent(), true);
        Stripe::setApiKey('sk_test_51NVqQtKRXD3JBQTyC0Yk1Qh9fZEb9e9sfKMDqp7TdShloj5KMCbLkmnFNW7dCJn4Knzsfab67U1tNs0Jibk6NXa500wZvliJnx');

        $token = str_replace('Bearer ', '', $authorizationHeader);
        try {
            $decodedToken = $this->jwtEncoder->decode($token);
            if (!$decodedToken) {
                throw new UnauthorizedHttpException("Bearer", "Invalid or expired token");
            }

            // Extrait l'identifiant de l'utilisateur à partir des données du token
            $userIdFromToken = $decodedToken['user_id']; // Assurez-vous que 'username' est la clé correcte

            // Extrait l'identifiant de l'utilisateur à partir du corps de la requête

            $userIdFromRequest = $data['userId'];

            // Vérifie que l'ID de l'utilisateur du token correspond à celui de la requête
            if ("$userIdFromToken" !== $userIdFromRequest) {
                throw new UnauthorizedHttpException("Bearer", "User ID does not match the one from the token");
            }

            // Vérifie l'existence de l'utilisateur et de la prestation
            $user = $this->userRepository->find($userIdFromRequest);
            $prestation = $this->prestationRepository->find($data['idPresta']);

            if (!$user || !$prestation) {
                throw new NotFoundHttpException("User or Prestation not found");
            }

            // Crée une nouvelle réservation
            $booking = new Booking();
            $booking->setBookedBy($user);
            $booking->setPrestation($prestation);

            $bookingDateFromRequest = $data['prestaSchedule'];
            $datesArray = json_decode($bookingDateFromRequest);
            $dateString = $datesArray[0];
            $booking->setBookingDate(new \DateTime($dateString)); // Assurez-vous de traiter correctement le format de la date

            $this->manager->persist($booking);
            $this->manager->flush();

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $data['productName'],
                            'images' => [$data['imageUrl']],
                        ],
                        'unit_amount' => $data['price']*100,

                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $_ENV["FRONT_URL"]."/reservations",
                'cancel_url' => $_ENV["FRONT_URL"].'/payment/errorPayment',
            ]);

            return $this->json(['id' => $session->id]);
        } catch (\Exception $e) {

            throw new UnauthorizedHttpException("Bearer", "An error occurred: " . $e->getMessage());
        }


    }

}
