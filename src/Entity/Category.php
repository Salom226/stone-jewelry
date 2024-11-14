<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product_simple'])]
    private ?int $id = null;

    #[ORM\Column(length: 255,unique: true)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: "category", targetEntity: Product::class)]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
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
            $product->setCategory($this); // Assure que le produit connaît sa catégorie
        }
        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // Détache la catégorie côté produit si nécessaire
            if ($product->getCategory() === $this) {
                $product->setCategory(null);
            }
        }
        return $this;
    }

    // /**
    //  * @return Collection<int, SubCategory>
    //  */
    // public function getSubCategories(): Collection
    // {
    //     return $this->subCategories;
    // }

    // public function addSubCategory(SubCategory $subCategory): static
    // {
    //     if (!$this->subCategories->contains($subCategory)) {
    //         $this->subCategories->add($subCategory);
    //         $subCategory->setCategory($this);
    //     }

    //     return $this;
    // }

    // public function removeSubCategory(SubCategory $subCategory): static
    // {
    //     if ($this->subCategories->removeElement($subCategory)) {
    //         // set the owning side to null (unless already changed)
    //         if ($subCategory->getCategory() === $this) {
    //             $subCategory->setCategory(null);
    //         }
    //     }

    //     return $this;
    // }
}
