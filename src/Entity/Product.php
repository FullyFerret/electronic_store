<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Category as Category;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Product
{

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="product_name", type="string", length=100, unique=true)
     * @Assert\NotBlank(message="Product must have a name")
     * @Assert\Length(max="100", maxMessage="Product name is too long")
     */
    private $name;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="products")
     * @ORM\JoinColumn(name="category", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=5, nullable=true)
     * @Assert\Regex(pattern="/^A\d{4}$/", message="SKU must be in the format A#### (A followed by 4 digits)")
     */
    private $sku;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Type(type="float", message="Price must in currency format XX.XX")
     * @Assert\GreaterThanOrEqual(value="0.00", message="Price cannot be less than 0.00")
     */
    private $price;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint")
     * @Assert\GreaterThanOrEqual(value="0", message="Quantity cannot be less than 0")
     */
    private $quantity = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $modified_at;


    /**
     * Product constructor.
     */
    function __construct()
    {
        $this->quantity = 0;
        $this->created_at = new \DateTime();
    }

    /**
     * @return array
     */
    public function serialized(): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "category" => empty($this->category) ? null : $this->category->getName(),
            "sku" => $this->sku,
            "price" => $this->price,
            "quantity" => $this->quantity,
            "created_at" => $this->created_at->format(DATE_ISO8601),
            "modified_at" => empty($this->modified_at) ? null : $this->modified_at->format(DATE_ISO8601)
        ];
    }

    /**
     * @return int|null
     */
    public function getId(): ? int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getName(): ? string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Product
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return \App\Entity\Category|null
     */
    public function getCategory(): ? Category
    {
        return $this->category;
    }

    /**
     * @param \App\Entity\Category $category
     * @return Product|null
     */
    public function setCategory(Category $category): ? self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSku(): ? string
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     * @return Product
     */
    public function setSku(string $sku): self
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getPrice(): ? float
    {
        return $this->price;
    }

    /**
     * @param float|null $price
     * @return Product
     */
    public function setPrice(? float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getQuantity(): ? int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return Product
     */
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }


    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ? \DateTimeInterface
    {
        return $this->created_at;
    }

    /**
     * @param \DateTimeInterface|null $created_at
     * @return Product
     */
    public function setCreatedAt(? \DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }


    /**
     * @return \DateTimeInterface|null
     */
    public function getModifiedAt(): ? \DateTimeInterface
    {
        return $this->modified_at;
    }

    /**
     * @param \DateTimeInterface|null $modified_at
     * @return Product
     */
    public function setModifiedAt(? \DateTimeInterface $modified_at): self
    {
        $this->modified_at = $modified_at;

        return $this;
    }

    /**
     * @return Product
     *
     * @ORM\PreUpdate()
     */
    public function updateModifiedAt(): self
    {
        $this->modified_at = new \DateTime();

        return $this;
    }
}
