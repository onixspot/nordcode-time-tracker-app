<?php

namespace App\Controller;

use App\Component\Task\Context\ReportContext;
use App\Component\Task\Response\CsvResponse;
use App\Entity\Task;
use App\Form\TaskFormType;
use App\Form\TaskReportFormType;
use App\Repository\TaskRepository;
use Doctrine\ORM\Query\QueryException;
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
    /**
     * @Route("/", name="task_index", methods={"GET"})
     * @throws QueryException
     */
    public function index(TaskRepository $taskRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $tasks = $paginator->paginate(
            $taskRepository
                ->createQueryBuilder()
                ->getQuery(),
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
     */
    public function new(Request $request): Response
    {
        $task = new Task();
        $task->setUser($this->getUser());
        $form = $this->createForm(TaskFormType::class, $task);
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
     * @Route("/{id}", name="task_show", methods={"GET"}, requirements={"id"="\d+"})
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
     * @Route("/{id}/edit", name="task_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     */
    public function edit(Task $task, Request $request): Response
    {
        $form = $this->createForm(TaskFormType::class, $task);
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
     * @Route("/{id}", name="task_delete", methods={"POST"}, requirements={"id"="\d+"})
     */
    public function delete(Task $task, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('task_index');
    }

    /**
     * @Route("/export", name="task_export", methods={"GET", "POST"})
     * @return Response|CsvResponse
     */
    public function export(Request $request)
    {
        $reportContext = new ReportContext();
        $form          = $this->createForm(TaskReportFormType::class, $reportContext);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ReportContext $context */
            $context = $form->getData();

            return new CsvResponse($context);

            // return new CsvResponse(
            //     array_merge(
            //         [
            //             ['Id', 'Title', 'Comment', 'Date'], // , 'Time Spent'
            //         ],
            //         $context
            //             ->getTasks()
            //             ->map(
            //                 static function (Task $task) {
            //                     return [
            //                         $task->getId(),
            //                         $task->getTitle() ?? '',
            //                         $task->getComment() ?? '',
            //                         $task->getDate()->format('d.m.Y'),
            //                     ];
            //                 }
            //             )
            //             ->toArray(),
            //         [
            //             ['', '', 'Total', $context->getTotalTimeSpent()],
            //         ]
            //     )
            // );
        }

        return $this->render(
            'task/export.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
