<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/task")
 */
class TaskController extends AbstractController
{
    public function __construct()
    {
    }

    /**
     * @Route("/", name="task_index", methods={"GET"})
     * @param TaskRepository $taskRepository
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(TaskRepository $taskRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $taskRepository->createQueryBuilder('t')
            ->where('t.user_id = :user_id')
            ->setParameter('user_id', $this->getUser())
            ->getQuery();

        $tasks = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render(
            'task/index.html.twig',
            [
                'tasks' => $tasks,
            ]
        );
    }

    /**
     * @Route("/new", name="task_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $task = new Task();
        $task->setUserId($this->getUser());
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'task/new.html.twig',
            [
                'task' => $task,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="task_show", methods={"GET"})
     * @param Task $task
     * @return Response
     */
    public function show(Task $task): Response
    {
        return $this->render(
            'task/show.html.twig',
            [
                'task' => $task,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="task_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Task $task
     * @return Response
     */
    public function edit(Request $request, Task $task): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'task/edit.html.twig',
            [
                'task' => $task,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="task_delete", methods={"POST"})
     * @param Request $request
     * @param Task $task
     * @return Response
     */
    public function delete(Request $request, Task $task): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('task_index');
    }
}
