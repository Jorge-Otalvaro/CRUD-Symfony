<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = "4",
     *      max = "10",
     *      minMessage = "El codigo por lo menos debe tener {{ limit }} caracteres de largo",
     *      maxMessage = "El codigo no puede tener más de {{ limit }} caracteres de largo"
     * )
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = "4",
     *      max = "120",
     *      minMessage = "El nombre por lo menos debe tener {{ limit }} caracteres de largo",
     *      maxMessage = "El nombre no puede tener más de {{ limit }} caracteres de largo"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(
     *      min = "4",
     *      max = "120",
     *      minMessage = "La descripcion por lo menos debe tener {{ limit }} caracteres de largo",
     *      maxMessage = "La descripcion no puede tener más de {{ limit }} caracteres de largo"
     * )
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $brand;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new UniqueEntity([
            'fields' => ['name'],
            'errorPath' => 'name',
            'message' => 'El nombre del producto ya esta en uso',
        ]));

        $metadata->addPropertyConstraint('name', new NotBlank([
            'message' => 'El campo es obligatorio / no puede estar en null',
        ]));

        $metadata->addPropertyConstraint('code', new NotBlank([
            'message' => 'El campo es obligatorio / no puede estar en null',
        ]));

        $metadata->addPropertyConstraint('description', new NotBlank([
            'message' => 'El campo es obligatorio / no puede estar en null',
        ]));

        $metadata->addPropertyConstraint('brand', new NotBlank([
            'message' => 'El campo es obligatorio / no puede estar en null',
        ]));

        $metadata->addPropertyConstraint('price', new NotBlank([
            'message' => 'El campo es obligatorio / no puede estar en null',
        ]));

        $metadata->addConstraint(new UniqueEntity([
            'fields' => ['code'],
            'errorPath' => 'code',
            'message' => 'El código del producto ya esta en uso',
        ]));

        $metadata->addPropertyConstraint('price', new Assert\Positive([
            'message' => 'El precio debe ser un número válido.',
        ]));

        $metadata->addPropertyConstraint('code', new Assert\Regex([
            'pattern' => '/[^A-Za-z0-9\-]/',
            'match' => false,
            'message' => 'El código no puede contener caracteres especiales ni espacios',
        ]));
    }
}
