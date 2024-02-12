<?php

namespace App\Dto;

class RegisterPrestataireDto
{
    public function __construct(public string $name,
    public string $adresse,
    public string $contactInfos,
    public string $description,
    public string $sector,
    public string $numKbis)
    {

    }


    public function getName(): string
    {
        return $this->name;
    }

    public function getAdresse(): string
    {
        return $this->adresse;
    }

    public function getContactInfos(): string
    {
        return $this->contactInfos;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getSector(): string
    {
        return $this->sector;
    }

    public function getNumKbis(): string
    {
        return $this->numKbis;
    }


}