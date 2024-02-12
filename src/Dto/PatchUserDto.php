<?php

namespace App\Dto;
use Symfony\Component\Validator\Constraints as Assert;


class PatchUserDto {

    public function __construct(
        public string $firstname ,
        public string $lastname
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

//    /**
//     * @return string
//     */
//    public function getEmail(): string
//    {
//        return $this->email;
//    }
}