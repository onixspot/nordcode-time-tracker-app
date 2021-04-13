<?php

namespace App\Form;

use App\Component\Task\Context\ReportContext;
use App\Entity\Task;
use App\Repository\TaskRepository;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\QueryException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskReportFormType extends AbstractType implements DataMapperInterface, DataTransformerInterface
{
    private TaskRepository $taskRepository;

    private ReportContext $context;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $yearsRange = range(2000, date('Y'));
        $dateEnd    = new DateTimeImmutable();
        $dateStart  = $dateEnd->sub(new DateInterval('P1D'));

        $builder
            ->add(
                'date_start',
                DateType::class,
                [
                    'widget'   => 'single_text',
                    'years'    => $yearsRange,
                    'data'     => $dateStart,
                    'required' => true,
                    'label'    => 'From',
                ]
            )
            ->add(
                'date_end',
                DateType::class,
                [
                    'widget' => 'single_text',
                    'years'  => $yearsRange,
                    'data'   => $dateEnd,
                    'label'  => 'To',
                ]
            )
            ->add(
                'format',
                ChoiceType::class,
                [
                    'choices'     => ReportContext::FORMATS,
                    'label'       => 'Format',
                    'placeholder' => 'Chose document format',
                ]
            );

        $builder->setDataMapper($this->setContext($options['data']));
        // ->addModelTransformer($this);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => ReportContext::class,
                'required'   => true,
            ]
        );
    }

    public function mapDataToForms($viewData, $forms)
    {
    }

    /**
     * @throws QueryException
     */
    public function mapFormsToData($forms, &$viewData): void
    {
        $forms = iterator_to_array($forms);

        $ctx = $this->getContext()
            ->setFormat($forms['format']->getData())
            ->setDateStart($forms['date_start']->getData())
            ->setDateEnd($forms['date_end']->getData());

        $tasks = $this->getTasks();
        $ctx
            ->setTotalTimeSpent($this->getTotalTimeSpent($tasks))
            ->setTasks(
                new ArrayCollection(
                    array_map(
                        static function ($task) {
                            return (new Task((int)$task['id']))
                                ->setTitle((string)$task['title'])
                                ->setComment((string)$task['comment'])
                                ->setDate(new DateTime($task['date']));
                            // ->setTimeSpent($task['timeSpent']);
                        },
                        $tasks
                    )
                )
            );

        $viewData = $ctx;
    }

    /**
     * @return ReportContext
     */
    public function getContext(): ReportContext
    {
        return $this->context;
    }

    /**
     * @param ReportContext $context
     * @return TaskReportFormType
     */
    public function setContext(ReportContext $context): TaskReportFormType
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @throws QueryException
     */
    private function getTasks()
    {
        $ctx = $this->getContext();

        return $this->taskRepository->findInDateRange($ctx->getDateStart(), $ctx->getDateEnd());
    }

    private function getTotalTimeSpent(array &$tasks): int
    {
        ['timeSpent' => $timeSpent] = array_splice($tasks, -1, 1)[0];

        if (!$timeSpent) {
            return 0;
        }

        return (int)$timeSpent;
    }

    public function transform($value)
    {
    }

    public function reverseTransform($value)
    {
    }
}
