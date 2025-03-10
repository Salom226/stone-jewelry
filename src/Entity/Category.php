<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;


#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['category_list', 'product_simple'])]
    #[MaxDepth(1)]
    private ?int $id = null;

    #[ORM\Column(length: 255,unique: true)]
    #[Groups(['category_list', 'product_simple'])]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: "category", targetEntity: Product::class)]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    #[Groups(['category_list'])]
    public function getProductNames(): array
    {
        return $this->products->map(fn(Product $product) => $product->getName())->toArray();
    }


    // /**
    //  * @var Collection<int, SubCategory>
    //  */
    // #[ORM\OneToMany(targetEntity: SubCategory::class, mappedBy: 'category')]
    // private Collection $subCategories;


    public function __toString():string
    {
        return $this->name;
    }
    // public function __construct()
    // {
    //     $this->subCategories = new ArrayCollection();
    // }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
     /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setCategory($this);
        }
        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            if ($product->getCategory() === $this) {
                $product->setCategory(null);
            }
        }
        return $this;
    }
}
