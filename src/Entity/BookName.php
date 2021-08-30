<?php

namespace App\Entity;

use App\Repository\BookNameRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass=BookNameRepository::class)
 */
class BookName
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @JMS\Groups({"book:write", "book:read:all-locales"})
     * @OA\Property(example="Название книги")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=2)
     * @JMS\Groups({"book:write", "book:read:all-locales"})
     * @OA\Property(example="en")
     */
    private $locale;

    /**
     * @ORM\ManyToOne(targetEntity=Book::class, inversedBy="bookNames")
     * @ORM\JoinColumn(nullable=false)
     */
    private $book;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;

        return $this;
    }
}
