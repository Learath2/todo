<?php

namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user_list", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index()
    {
    	$users = $this->getDoctrine()->getRepository(User::class)->findAll();
		return $this->json($users, 200, [], ["groups" => "default"]);
    }

	/**
	 * @Route("/user/{id}", name="user_get", methods={"GET"}, requirements={"id"="\d+"})
	 */
    public function user(User $user)
    {
    	return $this->json($user, 200, [], ["groups" => "default"]);
    }

	/**
	 * @Route("/user", name="user_create", methods={"POST"})
	 * @IsGranted("ROLE_ADMIN")
	 */
    public function userCreate(Request $request, SerializerInterface $serializer)
    {
    	if($request->getContentType() !== "json")
    		throw new BadRequestHttpException(sprintf("Bad content-type: %s", $request->getContentType()));

    	$json = $request->getContent();
    	$user = $serializer->deserialize($json, User::class, 'json');

    	$em = $this->getDoctrine()->getManager();
    	$em->persist($user);
    	$em->flush();

    	return $this->json($user, 201, [], ["groups" => "default"]);
    }
}
