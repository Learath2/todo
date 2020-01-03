<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
	private $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * @inheritDoc
	 */
	public function start(Request $request, AuthenticationException $authException = null)
	{
		return new JsonResponse(["message" => "Unauthorized"], Response::HTTP_UNAUTHORIZED);
	}

	/**
	 * @inheritDoc
	 */
	public function supports(Request $request)
	{
		return $request->headers->has("Authorization");
	}

	/**
	 * @inheritDoc
	 */
	public function getCredentials(Request $request)
	{
		return $request->headers->get("Authorization");
	}

	/**
	 * @inheritDoc
	 */
	public function getUser($credentials, UserProviderInterface $userProvider)
	{
		$authorization = explode(" ", $credentials);
		if($authorization[0] !== "Bearer")
			return null;

		return $this->entityManager->getRepository(User::class)
			->findOneBy(["apiToken" => $authorization[1]]);
	}

	/**
	 * @inheritDoc
	 */
	public function checkCredentials($credentials, UserInterface $user)
	{
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
	{
		return new JsonResponse(["message" => "Unauthorized"], Response::HTTP_UNAUTHORIZED);
	}

	/**
	 * @inheritDoc
	 */
	public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
	{
		return null;
	}

	/**
	 * @inheritDoc
	 */
	public function supportsRememberMe()
	{
		return false;
	}
}