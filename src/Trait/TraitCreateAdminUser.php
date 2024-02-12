<?php


use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

trait TraitCreateAdminUser {


    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function CreateAdmin()
    {
        $admin = new User();
        $admin->setPassword($this->passwordHasher->hashPassword($admin, "123456"));
        $admin->setFirstname("admin");
        $admin->setLastname("admin");
        $admin->setEmail("admin@admin.com");
        $admin->setRoles(['ROLE_ADMIN']);

    }

}