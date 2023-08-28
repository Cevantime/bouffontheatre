<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    /**
     * @param UserPasswordHasherInterface $hasher
     */
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@bouffon.com');
        $admin->setFirstname("Thibault");
        $admin->setLastname("Truffert");
        $admin->setRoles(["ROLE_SUPER_ADMIN"]);

        $admin->setPassword($this->hasher->hashPassword($admin, "root"));
        $admin->setNewsletter(false);

        $manager->persist($admin);

        $manager->flush();
    }
}
