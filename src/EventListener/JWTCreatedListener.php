<?php
// src/EventListener/JWTCreatedListener.php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTCreatedListener
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();

        $payload = $event->getData();

        // Ici, vous pouvez ajouter des informations personnalisées au payload
        // Par exemple, ajout de l'ID de l'utilisateur
        $user = $event->getUser();
        $payload['user_id'] = $user->getId();  // Assurez-vous que votre objet User a une méthode getId()

        // Vous pouvez ajouter d'autres informations personnalisées ici
        // $payload['custom_info'] = 'value';

        $event->setData($payload);
    }
}
