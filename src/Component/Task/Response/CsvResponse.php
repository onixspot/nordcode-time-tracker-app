<?php

namespace App\Component\Task\Response;

use App\Component\Task\Context\ReportContext;
use App\Entity\Task;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;

class CsvResponse extends AbstractStreamedResponse
{
    protected string $data;

    private ReportContext $context;

    public function __construct(ReportContext $context, $status = Response::HTTP_OK, $headers = [])
    {
        parent::__construct(null, $status, $headers);

        $this->headers->set('Content-Type', 'text/csv');
        $this->headers->set(
            'Content-Disposition',
            HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_INLINE,
                'report.csv'
            )
        );

        $this->setContext($context);
    }

    public function setData(ArrayCollection $collection): self
    {
        $output = fopen('php://temp', 'rb+');

        $collection = $collection
            ->map(
                function (Task $task) use ($output) {
                    $row = [
                        $task->getId(),
                        $task->getTitle() ?? '',
                        $task->getComment() ?? '',
                        $task->getDate()->format('d.m.Y'),
                    ];

                    fputcsv($output, $row);

                    return $row;
                }
            );

        rewind($output);

        $collection = $collection->map(
            function () use ($output) {
                return fgets($output);
            }
        );


        return $this->setContent(implode(PHP_EOL, $collection->toArray()));
    }

    public function getContext(): ReportContext
    {
        return $this->context;
    }

    public function setContext(ReportContext $context): self
    {
        $this->context = $context;
        $this->setData($this->getContext()->getTasks());

        return $this;
    }
}