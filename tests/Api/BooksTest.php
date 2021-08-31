<?php

declare(strict_types=1);

namespace App\Tests\Api;


use App\DataFixtures\AppFixtures;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class BooksTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = $this->getContainer();
        /** @var EntityManager $em */
        $em = $container->get('doctrine')->getManager();

        $meta = $em->getMetadataFactory()->getAllMetadata();

        $connection = $em->getConnection();
        $tmpConnection = DriverManager::getConnection($connection->getParams());

        if (!\in_array($connection->getDatabase(), $tmpConnection->getSchemaManager()->listDatabases())) {
            $tmpConnection->getSchemaManager()->createDatabase($connection->getDatabase());
        }
        if (!empty($meta)) {
            $tool = new SchemaTool($em);
            $tool->dropSchema($meta);
            try {
                $tool->createSchema($meta);
            } catch (ToolsException $e) {
                throw new \InvalidArgumentException("Database schema is not buildable: {$e->getMessage()}", $e->getCode(), $e);
            }
        }

        $loader = new Loader();
        $loader->addFixture(new AppFixtures());

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures());
    }

    public function testCreateBook(): void
    {
        $client = $this->client;

        $client->request('POST', '/api/books', [], [], [], json_encode([
            'authors' => [
                ['id' => 7]
            ],
            'names' => [
                [
                    'name' => 'Название книги на русском',
                    'locale' => 'ru',
                ],
                [
                    'name' => 'Name of the book in english',
                    'locale' => 'en',
                ],
            ],
        ]));

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
        self::assertResponseHeaderSame('content-type', 'application/json');

        $responseArray = json_decode($client->getResponse()->getContent(), true);
        $array = [
            'id' => 721,
            'authors' => [
                [
                    'id' => 7,
                    'name' => 'Братья Гримм'
                ],
            ],
            'book_names' => [
                [
                    'name' => 'Название книги на русском',
                    'locale' => 'ru',
                ],
                [
                    'name' => 'Name of the book in english',
                    'locale' => 'en',
                ],
            ],
        ];
        self::assertSame($array, $responseArray);
    }

    public function testGetAllBooksRu(): void
    {
        $client = $this->client;

        $client->request('GET', '/ru/api/books', ['limit' => 1000]);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertResponseHeaderSame('content-type', 'application/json');

        $responseArray = json_decode($client->getResponse()->getContent(), true);

        self::assertCount(720, $responseArray);

        $array = [
            "name" => "Статья мистера Блока",
            "id" => 1,
            "authors" => [
                [
                    "id" => 1,
                    "name" => "Марк Твен"
                ]
            ]
        ];

        self::assertSame($array, $responseArray[0]);
    }

    public function testGetBookRu(): void
    {
        $client = $this->client;

        $client->request('GET', '/ru/api/books/1', []);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertResponseHeaderSame('content-type', 'application/json');

        $responseArray = json_decode($client->getResponse()->getContent(), true);

        $array = [
            "name" => "Статья мистера Блока",
            "id" => 1,
            "authors" => [
                [
                    "id" => 1,
                    "name" => "Марк Твен"
                ]
            ]
        ];

        self::assertSame($array, $responseArray);
    }
}