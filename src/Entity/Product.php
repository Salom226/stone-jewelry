<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product_simple', 'product_detail'])]
    #[MaxDepth(1)] 
    private ?int $id = null;

    #[ORM\Column(length: 255, unique:true)]
    #[Groups(['product_simple', 'product_detail'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['product_detail'])] 
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['product_simple', 'product_detail'])]
    private ?float $price = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: "products")]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['product_detail'])]
    private ?Category $category = null;

    /**
     * @var Collection<int, subCategory>
     */
     #[ORM\ManyToMany(targetEntity: SubCategory::class, inversedBy: 'products')]
     #[Groups(['product_detail'])]
     private Collection $subCategories;

     #[ORM\Column(type: "array", nullable: true)]
     #[Groups(['product_simple', 'product_detail'])]
     private ?array $images = [];

    #[ORM\Column]
    #[Groups(['product_detail'])]
    private ?int $stock = null;

    /**
     * @var Collection<int, AddProductHistory>
     */
    #[ORM\OneToMany(targetEntity: AddProductHistory::class, mappedBy: 'product')]
    private Collection $addProductHistories;

    /**
     * @var Collection<int, OrderProducts>
     */
    #[ORM\OneToMany(targetEntity: OrderProducts::class, mappedBy: 'product')]
    private Collection $orderProducts;

    public function __construct()
    {
        $this->subCategories = new ArrayCollection();
        $this->addProductHistories = new ArrayCollection();
        $this->orderProducts = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }


    /**
     * @return Collection<int, subCategory>
     */
    public function getSubCategories(): Collection
    {
        return $this->subCategories;
    }

    public function addSubCategory(subCategory $subCategory): static
    {
        if (!$this->subCategories->contains($subCategory)) {
            $this->subCategories->add($subCategory);
        }

        return $this;
    }

    public function removeSubCategory(subCategory $subCategory): static
    {
        $this->subCategories->removeElement($subCategory);

        return $this;
    }

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(?array $images): self
    {
        $this->images = $images;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * @return Collection<int, AddProductHistory>
     */
    public function getAddProductHistories(): Collection
    {
        return $this->addProductHistories;
    }

    public function addAddProductHistory(AddProductHistory $addProductHistory): static
    {
        if (!$this->addProductHistories->contains($addProductHistory)) {
            $this->addProductHistories->add($addProductHistory);
            $addProductHistory->setProduct($this);
        }

        return $this;
    }

    public function removeAddProductHistory(AddProductHistory $addProductHistory): static
    {
        if ($this->addProductHistories->removeElement($addProductHistory)) {
            if ($addProductHistory->getProduct() === $this) {
                $addProductHistory->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, OrderProducts>
     */
    public function getOrderProducts(): Collection
    {
        return $this->orderProducts;
    }

    public function addOrderProduct(OrderProducts $orderProduct): static
    {
        if (!$this->orderProducts->contains($orderProduct)) {
            $this->orderProducts->add($orderProduct);
            $orderProduct->setProduct($this);
        }

        return $this;
    }

    public function removeOrderProduct(OrderProducts $orderProduct): static
    {
        if ($this->orderProducts->removeElement($orderProduct)) {
            if ($orderProduct->getProduct() === $this) {
                $orderProduct->setProduct(null);
            }
        }

        return $this;
    }
}
