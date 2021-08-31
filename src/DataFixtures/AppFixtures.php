<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\BookName;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture implements FixtureGroupInterface
{

    public static function getGroups(): array
    {
        return ['dev'];
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadAuthors($manager);
        $this->loadBooks($manager);
    }

    private function loadAuthors(ObjectManager $manager): void
    {
        foreach ($this->getAuthorData() as $name) {
            $author = new Author();
            $author->setName($name);
            $manager->persist($author);
        }
        $manager->flush();
    }

    private function loadBooks(ObjectManager $manager): void
    {
        foreach ($this->getBooksData() as $bookData) {
            $book = new Book();
            foreach ($bookData['names'] as $nameItem) {
                $bookName = new BookName();
                $bookName->setName($nameItem['name']);
                $bookName->setLocale($nameItem['locale']);
                $book->addBookName($bookName);
            }
            foreach ($bookData['authors'] as $authorId) {
                $author = $manager->getRepository(Author::class)->find($authorId);
                $book->addAuthor($author);
            }
            $manager->persist($book);
        }
        $manager->flush();
    }


    private function getAuthorData(): array
    {
        return json_decode(file_get_contents(dirname(__FILE__) . '/authors.json'), true);
    }

    private function getBooksData(): array
    {
        return json_decode(file_get_contents(dirname(__FILE__) . '/books.json'), true);
    }
}
