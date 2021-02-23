<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
     * @var User 
     */
    private $user;

    /**
     * ApiController constructor.
     * @param EntityManagerInterface $objectManager
     * @param RequestStack $request
     */
    public function __construct(EntityManagerInterface $objectManager, RequestStack $request)
    {
        $this->taskRepository = $objectManager->getRepository(Task::class);
        $this->objectManager = $objectManager;

        $apiToken = $request->getCurrentRequest()->headers->get('api-token');
        $user     = $this->objectManager->getRepository(User::class)->findOneBy([
            'apiKey' => $apiToken
        ]);
        if (!$user instanceof User) {
            throw new HttpException(401,'Unauthorized');
        }
        $this->user = $user;
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

        /*
        $tasks = $this->taskRepository->findAllAsArray();
        return $this->json($tasks);
        */

        $tasks = $this->taskRepository->findAllByUser($this->user);
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
        $task = new Task;
        $form = $this->createForm(TaskType::class, $task);
        $form->submit($request->request->all());

        /*
        $task->setName($request->request->get('name'));
        $task->setDescription($request->request->get('description'));
        $task->setDone($request->request->get('done'));
        */

        $this->objectManager->persist($task);
        $this->objectManager->flush();

        return $this->json($task);
    }

    public function updateTask($taskId, Request $request): Response
    {
        $task = $this->taskRepository->find($taskId);

        $form = $this->createForm(TaskType::class, $task);
        $form->submit($request->request->all());

        $this->objectManager->persist($task);
        $this->objectManager->flush();

        return $this->json($task);
    }
}
