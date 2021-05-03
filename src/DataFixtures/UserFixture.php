<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{

    private $encoded;

    public function __construct(UserPasswordEncoderInterface $encoded){
        $this->encoded = $encoded;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();

        $password = '123456';
        $passwordEncoded = $this->encoded->encodePassword($user, $password);

        // Aniss user account
        $user->setEmail('aniss@gmail.com');
        $user->setFirstName('Aniss');
        $user->setLastName('Nahim');
        $user->setPassword($passwordEncoded);
        $user->setBirthdate(date_create("1997-10-26"));
        $user->setRegistrationDate(date_create());
        
        $manager->persist($user);

        $manager->flush();
    }
}
