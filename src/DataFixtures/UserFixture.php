<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Event;
use App\Entity\Tag;
use App\Entity\EventTag;
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
        $faker = \Faker\Factory::create();

        $password = '123456';

        $tag = new Tag();
        $tag->setTagName("All");
        $manager->persist($tag);
        $manager->flush();

        for($i = 0; $i<3; $i++){
            $user = new User();
    
            $user->setEmail('user'.$i.'@gmail.com');
            $user->setFirstName($faker->firstName());
            $user->setLastName($faker->lastName);
            $passwordEncoded = $this->encoded->encodePassword($user, $password);
            $user->setPassword($passwordEncoded);
            $user->setBirthdate(new \DateTime($faker->dateTimeThisCentury->format('Y-m-d')));
            $user->setRegistrationDate(date_create());

            $manager->persist($user);
    
            $manager->flush();

            for($j = 0; $j<5; $j++){
                $event = new Event();

                $event->setTitle($faker->sentence($nbWords = 3));
                $event->setType($faker->word);
                $event->setDescription($faker->paragraph());
                $event->setCoverImage('eventTest.jpg');
                $event->setState("Published");
                $event->setCity($faker->city);
                $event->setAddress($faker->address);
                $event->setStartDate(date_create("2021-05-01"));
                $event->setEndDate($faker->dateTimeThisMonth());
                $event->setCreatedAt(new \DateTime());
                $event->setUpdatedAt(new \DateTime());
                $event->setOwner($user);

                $eventTag = new EventTag();
                $eventTag->setTag($tag);
                $eventTag->setTaggedEvent($event);

                $event->addEventTag($eventTag);

                $manager->persist($event);
                $manager->flush();
            }
        }
    }
}
