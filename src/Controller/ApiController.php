<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/tasks", name="tasks")
     * @param TaskRepository $taskRepository
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function index(TaskRepository $taskRepository, SerializerInterface $serializer): Response
    {
        /*
        $tasks = $taskRepository->findAll();
        $json = $serializer->serialize($tasks, 'json');
        return new Response($json);
        */

        $tasks = $taskRepository->findAllAsArray();
        return $this->json($tasks);
    }
}
