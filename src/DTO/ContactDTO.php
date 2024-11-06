<?php

namespace App\DTO;

use Symfony\component\Validator\Constraints as Assert;

class ContactDTO{

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 200)]
    public string $name = ''; //le nom de mon utilisateur

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 50)]
    public string $title;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 400)]
    public string $message;
}