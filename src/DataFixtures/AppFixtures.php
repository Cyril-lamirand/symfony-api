<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 10; $i++) {
            $task = new Task();
            $task->setName('Tâche '.$i);
            $task->setDescription('Description blabla '.$i);
            $task->setDone(0);

            $manager->persist($task);
        }

        $manager->flush();
    }
}
