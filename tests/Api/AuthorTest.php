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

class AuthorTest extends WebTestCase
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

    public function testCreateAuthor(): void
    {
        $client = $this->client;

        $client->request('POST', '/api/author', [], [], [], json_encode([
            'name' => 'Автор А.А.'
        ]));

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
        self::assertResponseHeaderSame('content-type', 'application/json');

        $responseArray = json_decode($client->getResponse()->getContent(), true);
        $array = [
            'id' => 110,
            'name' => 'Автор А.А.'

        ];
        self::assertSame($array, $responseArray);
    }

}