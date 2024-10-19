<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateUserDto
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $username;

    #[Assert\NotBlank]
    public string $password;

    #[Assert\NotBlank]
    public string $firstName;

    #[Assert\NotBlank]
    public string $lastName;

    public array $roles;
}