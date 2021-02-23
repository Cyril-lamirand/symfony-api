<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var PasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * AppFixtures constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail('User'.$i.'@gmail.com');
            $user->setPassword($this->passwordEncoder->encodePassword($user,'password'));
            $user->setApiKey('ApiKeyTest'.$i);
            $manager->persist($user);
        }
        $manager->flush();

        for ($i = 1; $i <= 10; $i++) {
            $task = new Task();
            $task->setName('Tâche '.$i);
            $task->setDescription('Description blabla '.$i);
            $task->setDone(0);
            $manager->persist($task);
        }
    }
}
