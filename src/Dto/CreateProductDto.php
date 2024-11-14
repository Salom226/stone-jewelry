<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateProductDto
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $name;
    public ?string $description = null;

    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    public float $price;

    #[Assert\Length(max: 255)]
    public ?string $image = null;

    #[Assert\PositiveOrZero]
    public ?int $stock = 0;

    #[Assert\NotBlank]
    public ?int $categoryId = null;

}
