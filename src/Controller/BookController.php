<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Book;
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
     *     ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @Model(type=Book::class, groups={"book:read"})
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
            ->setGroups('book:read');
        $json = $serializer->serialize($pagination->getItems(), 'json', $serializationContext);
        return new Response($json, 200, ['Content-Type' => 'application/json']);
    }
}
