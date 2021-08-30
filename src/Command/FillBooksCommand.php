<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\BookName;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DomCrawler\Crawler;

class FillBooksCommand extends Command
{
    protected static $defaultName = 'app:fill-books';
    protected static $defaultDescription = 'Заполнение книг с сайта';

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    private function getContent(string $url): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $html = curl_exec($ch);
        curl_close($ch);
        return $html;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $baseUrl = 'https://linguabooster.com';

        for ($page = 1; $page <= 60; $page++) {
            $url = $baseUrl . "/ru/en/books" . ($page == 1 ? '' : '/' . $page);
            $html = $this->getContent($url);
            $crawler = new Crawler($html);
            $nodeValues = $crawler->filter('div.rounded > a.no-underline.bg-gray-800 ')->each(function (Crawler $node, $i) {
                return $node->attr('href');
            });

            foreach ($nodeValues as $key => $nodeValue) {
                $url = $baseUrl . $nodeValue;
                $html = $this->getContent($url);
                $crawler = new Crawler($html);
                $nameRu = $crawler->filter('h1[itemprop="name"]')->first()->text();
                preg_match('/«(?P<name>.*)»/', $nameRu, $matches);
                if ($matches['name']) {
                    $nameRu = $matches['name'];
                }
                dump($nameRu);
                $nameEn = $crawler->filter('h2[itemprop="alternateName"]')->first()->text();
                dump($nameEn);
                $authorName = $crawler->filter('a[itemprop="author"]')->first()->text();
                dump($authorName);
                dump('---------- page: ' . $page . ' item: ' . ($key + 1));

                $book = new Book();
                $bookNameRu = new BookName();
                $bookNameRu->setName($nameRu)->setLocale('ru');
                $bookNameEn = new BookName();
                $bookNameEn->setName($nameEn)->setLocale('en');
                $book
                    ->addBookName($bookNameRu)
                    ->addBookName($bookNameEn);
                $this->em->persist($book);

                $author = $this->em->getRepository(Author::class)->findOneBy(['name' => $authorName]);
                if (!$author) {
                    $author = new Author();
                    $author->setName($authorName);
                }
                $book->addAuthor($author);
                $this->em->flush();
            }
        }

        //

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
