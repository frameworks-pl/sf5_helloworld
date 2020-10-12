<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Note;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $note = new Note();
        $note->setContent("This is just a test");
        $manager->persist($note);

        $manager->flush();
    }
}
