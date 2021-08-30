<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\BookName;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;
use function Symfony\Component\String\u;

class AppTestFixtures extends Fixture
{
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
        return [
            'Братья Гримм',
            'Антон Павлович Чехов',
            'О. Генри',
            'Джеймс Джойс',
            'Марк Твен',
            'Говард Филлипс Лавкрафт',
            'Джозеф Редьярд Киплинг',
            'Джек Лондон',
        ];
    }

    private function getBooksData(): array
    {
        return [
            [
                'authors' => [6],
                'names' => [
                    ['name' => 'Праздник', 'locale' => 'ru'],
                    ['name' => 'The Festival', 'locale' => 'en'],
                ]
            ],
            [
                'authors' => [7],
                'names' => [
                    ['name' => 'Дети Зодиака', 'locale' => 'ru'],
                    ['name' => 'The Children of the Zodiac', 'locale' => 'en'],
                ]
            ],
            [
                'authors' => [8],
                'names' => [
                    ['name' => 'Чун А-чун', 'locale' => 'ru'],
                    ['name' => 'Chun Ah Chun', 'locale' => 'en'],
                ]
            ],
            [
                'authors' => [5],
                'names' => [
                    ['name' => 'Назойливый завсегдатай', 'locale' => 'ru'],
                    ['name' => 'The Office Bore', 'locale' => 'en'],
                ]
            ],
        ];
    }
}
