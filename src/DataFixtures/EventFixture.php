<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Event;

class EventFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $event = new Event();

        $event->setTitle("Code Camp Event");
        $event->setType("Workshop");
        $event->setDescription("This is the fisrt event in this app, it only made for testing resonses Hope you'll enjoye it.");
        $event->setCoverImage("eventTest.jpg");
        $event->setState("coming soon");
        $event->setCity("Rabat");
        $event->setAddress("National school of computer science - Agdal Rabat");
        $event->setStartDate(date_create("2021-05-11"));
        $event->setEndDate(date_create("2021-05-16"));
        $event->setCreatedAt(new \DateTime());
        $event->setUpdatedAt(new \DateTime());

        $manager->persist($event);

        $manager->flush();
    }
}
