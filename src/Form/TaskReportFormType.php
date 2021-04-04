<?php

namespace App\Form;

use App\Form\Type\TaskReportType;
use App\Service\TaskService;
use DateInterval;
use DateTimeImmutable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskReportFormType extends AbstractType implements DataMapperInterface, DataTransformerInterface
{
    private TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $dateEnd    = new DateTimeImmutable();
        $dateStart  = $dateEnd->sub(new DateInterval('P1D'));
        $yearsRange = range(2000, date('Y'));

        $builder
            ->add(
                'date_start',
                DateType::class,
                [
                    'widget'   => 'single_text',
                    'years'    => $yearsRange,
                    'data'     => $dateEnd,
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
                    'data'   => $dateStart,
                    'label'  => 'To',
                ]
            )
            ->add(
                'format',
                ChoiceType::class,
                [
                    'choices'     => TaskReportType::FORMATS,
                    'label'       => 'Format',
                    'placeholder' => 'Chose document format',
                ]
            );

        $builder
            ->setDataMapper($this);
        // ->addModelTransformer($this);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => TaskReportType::class,
                'required'   => true,
            ]
        );
    }

    public function mapDataToForms($viewData, $forms)
    {
    }

    public function mapFormsToData($forms, &$viewData)
    {
        $forms    = iterator_to_array($forms);
        $viewData = (new TaskReportType())
            ->setFormat($forms['format']->getData())
            ->setDateStart($forms['date_start']->getData())
            ->setDateEnd($forms['date_end']->getData());
    }

    public function transform($value)
    {

    }

    public function reverseTransform($value)
    {

        // $this->taskService->generate()
        // dd($value);
    }
}
