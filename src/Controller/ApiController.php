<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    /**
     * @var TaskRepository
     */
    private $taskRepository;

    /**
     * @var EntityManagerInterface
     */
    private $objectManager;

    /**
     * ApiController constructor.
     * @param EntityManagerInterface $objectManager
     */
    public function __construct(EntityManagerInterface $objectManager)
    {
        $this->taskRepository = $objectManager->getRepository(Task::class);
        $this->objectManager = $objectManager;
    }

    /**
     * @Route("/tasks", name="api_get_tasks", methods={"GET"})
     * @return Response
     */
    public function index(): Response
    {
        /*
        $tasks = $taskRepository->findAll();
        $json = $serializer->serialize($tasks, 'json');
        return new Response($json);
        */

        /* We can remove 'SerializerInterface'
        $tasks = $taskRepository->findAll();
        return $this->json($tasks);
        */

        $tasks = $this->taskRepository->findAllAsArray();
        return $this->json($tasks);
    }

    /**
     * @Route("/tasks/{taskId}", name="api_get_task", methods={"GET"})
     * @param $taskId
     * @return Response
     */
    public function getTask($taskId): Response
    {
        $task = $this->taskRepository->find($taskId);

        if (!$task instanceof Task) {
            throw new NotFoundHttpException();
        } else {
            return $this->json($task);
        }
    }

    /**
     * @Route("/tasks/{taskId}", name="api_delete_task", methods={"DELETE"})
     * @param $taskId
     * @return Response
     */
    public function deleteTask($taskId): Response
    {
        $task = $this->taskRepository->find($taskId);

        $this->objectManager->remove($task);
        $this->objectManager->flush();

        return $this->json('Success Delete');
    }

    /**
     * @Route("/tasks", name="api_add_task", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function addTask(Request $request): Response
    {
        dd($request->request->all());

        $task = new Task();
        $task->setName($request->request->get('name'));
        $task->setDescription($request->request->get('description'));
        $task->setDone($request->request->get('done'));

        $this->objectManager->persist($task);
        $this->objectManager->flush();

        return $this->json($task);
    }
}
