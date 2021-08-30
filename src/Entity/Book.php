<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 */
class Book
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Groups({"book:read"})
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=Author::class, cascade={"persist"})
     * @JMS\Groups({"book:read","book:write"})
     */
    private Collection $authors;

    /**
     * @ORM\OneToMany(targetEntity=BookName::class, mappedBy="book", orphanRemoval=true, cascade={"persist"})
     * @JMS\Groups({"book:read:all-locales"})
     */
    private Collection $bookNames;

    /**
     * @return string
     *
     * @JMS\VirtualProperty()
     * @JMS\Groups({"book:read:locale"})
     * @JMS\Type("string")
     * @JMS\SerializedName("name")
     */
    public function getName(): string
    {
        return (isset($this->bookNames[0]) ? $this->bookNames[0]->getName() : '');
    }

    /**
     * @var BookName[]
     *
     * @JMS\Groups({"book:write"})
     * @JMS\Type("array<App\Entity\BookName>")
     * @JMS\SerializedName("names")
     * @JMS\Accessor(setter="setBookNames")
     *
     */
    private $names;

    public function setBookNames($names)
    {
        dump($names);
        $this->bookNames = new ArrayCollection();
        foreach ($names as $name) {
            $this->addBookName($name);
        }
    }

    public function __construct()
    {
        $this->authors = new ArrayCollection();
        $this->bookNames = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Author[]
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(Author $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors[] = $author;
        }

        return $this;
    }

    public function removeAuthor(Author $author): self
    {
        $this->authors->removeElement($author);

        return $this;
    }

    /**
     * @return Collection|BookName[]
     */
    public function getBookNames(): Collection
    {
        return $this->bookNames;
    }

    public function addBookName(BookName $bookName): self
    {
        if (!$this->bookNames->contains($bookName)) {
            $this->bookNames[] = $bookName;
            $bookName->setBook($this);
        }

        return $this;
    }

    public function removeBookName(BookName $bookName): self
    {
        if ($this->bookNames->removeElement($bookName)) {
            // set the owning side to null (unless already changed)
            if ($bookName->getBook() === $this) {
                $bookName->setBook(null);
            }
        }

        return $this;
    }
}
