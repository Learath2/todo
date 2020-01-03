<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TokenController extends AbstractController
{
	/**
	 * @Route("/token", name="token", methods={"GET"})
	 */
	public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder)
	{
		$username = $request->headers->get("php-auth-user");
		$password = $request->headers->get("php-auth-pw");

		if(!$username || !$password)
			return new JsonResponse(["headers" => $request->headers->all(), "message" => "Unauthorized"], Response::HTTP_UNAUTHORIZED);

		/** @var User $user */
		$user = $this->getDoctrine()->getRepository(User::class)
			->findOneBy(["username" => $username]);

		if(!$passwordEncoder->isPasswordValid($user, $password))
			return new JsonResponse(["message" => "Unauthorized", "username" => $username], Response::HTTP_UNAUTHORIZED);

		// TODO: Implement expiration
		if(!$user->getApiToken())
		{
			$token = bin2hex(openssl_random_pseudo_bytes(16));
			$user->setApiToken($token);

			$this->getDoctrine()->getManager()->flush();
		}

		return new JsonResponse(["token" => $user->getApiToken()], Response::HTTP_OK);
	}
}