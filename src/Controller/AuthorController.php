<?php

namespace App\Controller;

use App\Entity\Author;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

class AuthorController extends AbstractController
{
    /**
     * @OA\RequestBody(
     *    required=true,
     *    description="Объект книги",
     *    @OA\JsonContent(
     *       ref=@Model(type=Author::class, groups={"author:write"}),
     *    ),
     * ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @Model(type=Author::class, groups={"author:read"})
     *     )
     * @OA\Tag(name="Author")
     * @Route("/api/author", name="author_create", methods={"POST"})
     */
    public function authorCreate(Request $request, SerializerInterface $serializer): Response
    {
        $em = $this->getDoctrine()->getManager();
        $json = $request->getContent();
        $deserializationContext = DeserializationContext::create()->setGroups('author:write');

        $author =  $serializer->deserialize($json, Author::class , 'json', $deserializationContext);

        $em->persist($author);
        $em->flush();

        $serializationContext = SerializationContext::create()->setSerializeNull(true)
            ->setGroups(['author:read']);
        $json = $serializer->serialize($author, 'json', $serializationContext);
        return new Response($json, 201, ['Content-Type' => 'application/json']);
    }
}
