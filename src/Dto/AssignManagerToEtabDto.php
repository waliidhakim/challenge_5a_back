<?php

namespace App\Dto;

class AssignManagerToEtabDto
{
    public function __construct(

        public string $firstname,
        public string $lastname,
        public string $email
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
    public function getEmail(): string
    {
        return $this->email;
    }
}