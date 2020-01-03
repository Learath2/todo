<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class TodoController extends AbstractController
{
	/**
	 * @Route("/todo", name="todo_list", methods={"GET"})
	 * @IsGranted("ROLE_USER")
	 */
	public function index()
	{
		/** @var User $user */
		$user = $this->getUser();

		return $this->json($user->getTodos(),Response::HTTP_OK, [], ["groups" => "default"]);
	}

	/**
	 * @Route("/todo", name="todo_create", methods={"POST"})
	 * @IsGranted("ROLE_USER")
	 */
	public function todoCreate(Request $request, SerializerInterface $serializer)
	{
		if($request->getContentType() !== "json")
    		throw new BadRequestHttpException(sprintf("Bad content-type: %s", $request->getContentType()));

		$json = $request->getContent();

		/** @var Todo $todo */
		$todo = $serializer->deserialize($json, Todo::class, 'json'); // TODO: Validation

		/** @var User $user */
		$user = $this->getUser();
		$user->addTodo($todo);

		$em = $this->getDoctrine()->getManager();
		$em->persist($todo);
		$em->persist($user);
		$em->flush();

		return $this->json($todo, Response::HTTP_OK, [], ["groups" => "default"]);
	}
}