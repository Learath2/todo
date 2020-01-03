<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
	private $passwordEncoder;
	public function __construct(UserPasswordEncoderInterface $passwordEncoder)
	{
		$this->passwordEncoder = $passwordEncoder;
	}

	public function load(ObjectManager $manager)
    {
    	$user = new User();
    	$user->setUsername("admin");
    	$user->setPassword($this->passwordEncoder->encodePassword($user, "admin"));
    	$user->setApiToken("adminToken");
    	$user->setRoles(["ROLE_ADMIN"]);

    	$manager->persist($user);

		for($i = 0; $i < 10; $i++)
		{
			$user = new User();
			$user->setUsername(sprintf("user%d", $i));
			$user->setPassword($this->passwordEncoder->encodePassword($user, "test"));
			$user->setApiToken(sprintf("token%d", $i));

			$manager->persist($user);
		}

        $manager->flush();
    }
}
