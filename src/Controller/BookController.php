<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Book;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;


class BookController extends AbstractController
{
    /**

     *   @OA\Parameter(
     *     in="query",
     *     name="search",
     *     description="поиск по книге",
     *     required=false,
     *     @OA\Schema(type="string"),
     *   ),
     *    @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Лимит",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Номер страницы",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="_locale",
     *         in="path",
     *         description="Локаль",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             enum={"ru", "en"},
     *             default="en"
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\JsonContent(
     *            type="array",
     *            @OA\Items(ref=@Model(type=Book::class, groups={"book:read", "book:read:locale"})),
     *         )
     *
     *     )

     * @OA\Tag(name="Book")
     * @Route("{_locale}/api/books", name="books", methods={"GET"})
     */
    public function books(Request $request, SerializerInterface $serializer, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();
        $limit = 5;
        $offset = 0;
        if ($request->get('limit')) {
            $limit = (int)$request->get('limit');
        }
        if ($request->get('page')) {
            $offset = ($request->get('page', 1) - 1) * $limit;
        }
        $locale = $request->getLocale();
        $qb = $em->getRepository(Book::class)->findByQuery($request->get('search'), $locale);

        $pagination = $paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            $limit /*limit per page*/
        );

        $serializationContext = SerializationContext::create()->setSerializeNull(true)
            ->setGroups(['book:read', 'book:read:locales']);
        $json = $serializer->serialize($pagination->getItems(), 'json', $serializationContext);
        return new Response($json, 200, ['Content-Type' => 'application/json']);
    }

    /**
     *     @OA\Parameter(
     *         name="_locale",
     *         in="path",
     *         description="Локаль",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             enum={"ru", "en"},
     *             default="en"
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @Model(type=Book::class, groups={"book:read", "book:read:locale"})),
     *     )

     * @OA\Tag(name="Book")
     * @Route("{_locale}/api/books/{id}", name="book_item", methods={"GET"})
     */

    public function bookItem(Request $request, SerializerInterface $serializer, $id): Response
    {
        $em = $this->getDoctrine()->getManager();

        $locale = $request->getLocale();
        $book = $em->getRepository(Book::class)->findOneWithLocale($id, $locale);

        $serializationContext = SerializationContext::create()->setSerializeNull(true)
            ->setGroups(['book:read', 'book:read:locales']);
        $json = $serializer->serialize($book, 'json', $serializationContext);
        return new Response($json, 200, ['Content-Type' => 'application/json']);
    }


    /**
     * @OA\RequestBody(
     *    required=true,
     *    description="Объект книги",
     *    @OA\JsonContent(
     *       ref=@Model(type=Book::class, groups={"book:write"}),
     *    ),
     * ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @Model(type=Book::class, groups={"book:read", "book:read:all-locales"})
     *     )
     * @OA\Tag(name="Book")
     * @Route("/api/books", name="book_create", methods={"POST"})
     */


    public function bookCreate(Request $request, SerializerInterface $serializer): Response
    {
        $em = $this->getDoctrine()->getManager();
        $json = $request->getContent();
        $deserializationContext = DeserializationContext::create()->setGroups('book:write');

        $book = $serializer->deserialize($json, Book::class , 'json', $deserializationContext);

        $em->persist($book);
        $em->flush();

        $locale = $request->getLocale();
        $book = $em->getRepository(Book::class)->findOneWithLocale($book, $locale);

        $serializationContext = SerializationContext::create()->setSerializeNull(true)
            ->setGroups(['book:read', 'book:read:all-locales']);
        $json = $serializer->serialize($book, 'json', $serializationContext);
        return new Response($json, 201, ['Content-Type' => 'application/json']);
    }


}
