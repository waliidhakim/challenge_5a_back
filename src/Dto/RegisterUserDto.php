<?php


namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;


class RegisterUserDto
{

    public function __construct(

        public string $firstname,
        public string $lastname,
        public string $plainPassword,
        public string $confirmPassword,
        public string $email
//        #[Assert\Email]
//        public string $email
    )
    {

    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @return string
     */
    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    /**
     * @return string
     */
    public function getConfirmPassword(): string
    {
        return $this->confirmPassword;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
//    /**
//     * @return string
//     */
//    public function getEmail(): string
//    {
//        return $this->email;
//    }
}