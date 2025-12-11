<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class contactDto
{
    #[Assert\NotBlank()]
    #[Assert\Length(min: 3, max: 300)]
    public string $name = '';

    #[Assert\Email(
        message: 'l\'email {{ value }} n\'est pas valide.',
    )]
    public string $email = '';

    #[Assert\NotBlank()]
    #[Assert\Length(min: 3, max: 300)]
    public string $message = '';

    #[Assert\NotBlank()]
    public string $service = '';
}
