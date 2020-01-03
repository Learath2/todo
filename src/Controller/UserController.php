<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function index(UserRepository $repository)
    {
    	$users = $repository->findAll();
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
    public function userCreate(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
    	if($request->getContentType() !== "json")
    		throw new BadRequestHttpException(sprintf("Bad content-type: %s", $request->getContentType()));

    	$json = $request->getContent();
    	$user = $serializer->deserialize($json, User::class, 'json');

    	$entityManager->persist($user);
    	$entityManager->flush();

    	return $this->json($user, 201, [], ["groups" => "default"]);
    }
}
