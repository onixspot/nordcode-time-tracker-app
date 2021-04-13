<?php

namespace App\Controller;

use App\Component\Task\ReportContext;
use App\Component\Task\ReportGenerator;
use App\Entity\Task;
use App\Form\TaskFormType;
use App\Form\TaskReportFormType;
use App\Repository\TaskRepository;
use App\Response\CsvResponse;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/task")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/", name="task_index", methods={"GET"})
     * @param TaskRepository $taskRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(TaskRepository $taskRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $tasks = $paginator->paginate(
            $taskRepository->getUserTasksQuery($this->getUser()),
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
     * @Route("/{id}/edit", name="task_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     * @param Task $task
     * @param Request $request
     * @return Response
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
     * @param Task $task
     * @param Request $request
     * @return Response
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
     * @param Request $request
     * @return Response
     */
    public function export(Request $request, ReportGenerator $service): Response
    {
        $report = new ReportContext();
        $form = $this->createForm(TaskReportFormType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data          = $form->getData();
            // dd($data);
            return new CsvResponse([['Id', 'Title', 'Comment', 'Date', 'Time Spent'], ['B1', 'B2'], ['C1', 'C3']]);
            // $reportContext = $service
            //     ->generate($data['date_start'], $data['date_end'])
            //     ->map(
            //         function ($ctx) {
            //             explode(',', $ctx['tasks']);
            //         }
            //     );

            // return $this->render('task/report.html.twig', ['tasks' => []]);
        }

        return $this->render(
            'task/export.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
