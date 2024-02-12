<?php

namespace App\Dto;
use Symfony\Component\Validator\Constraints as Assert;
class RegisterEmployeeDto
{
    public function __construct(

        #[Assert\Email()]
        #[Assert\NotBlank(message: "Email field is required")]
        public string $email,

        #[Assert\Length(
            min: 6,
            minMessage: "Should contain at least 6 characters"
        )]
        #[Assert\NotBlank(message: "Password field is required")]
        public string $password,

        #[Assert\NotBlank(message: "Firstname field is required")]
        public ?string $firstname,

        #[Assert\NotBlank(message: "Lastname field is required")]
        public ?string $lastname,

        #[Assert\NotBlank(message: "Sould Specify the ID of the Establishement to be updated")]
        public int $establishmentId
    )
    {

    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function getEstablishmentId(): int
    {
        return $this->establishmentId;
    }

}