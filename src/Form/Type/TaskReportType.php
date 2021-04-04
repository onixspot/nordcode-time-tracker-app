<?php

namespace App\Form\Type;

use App\Entity\User;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;

class TaskReportType
{
    public const PDF_FORMAT   = 'PDF';
    public const CSV_FORMAT   = 'CSV';
    public const EXCEL_FORMAT = 'EXCEL';
    public const FORMATS      = [
        'Pdf'   => self::PDF_FORMAT,
        'Csv'   => self::CSV_FORMAT,
        'Excel' => self::EXCEL_FORMAT,
    ];

    private User $user;
    private ArrayCollection $tasks;
    private ?DateTimeInterface $dateStart;
    private ?DateTimeInterface $dateEnd;
    private ?DateTimeInterface $totalTimeSpent;
    private string $format;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getDateStart(): ?DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(DateTimeInterface $dateStart): TaskReportType
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(DateTimeInterface $dateEnd): TaskReportType
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): TaskReportType
    {
        $this->user = $user;

        return $this;
    }

    public function getTasks(): ArrayCollection
    {
        return $this->tasks;
    }

    public function setTasks(ArrayCollection $tasks): TaskReportType
    {
        $this->tasks = $tasks;

        return $this;
    }

    public function getTotalTimeSpent(): ?DateTimeInterface
    {
        return $this->totalTimeSpent;
    }

    public function setTotalTimeSpent(DateTimeInterface $totalTimeSpent): TaskReportType
    {
        $this->totalTimeSpent = $totalTimeSpent;

        return $this;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function setFormat(string $format): TaskReportType
    {
        $this->format = $format;

        return $this;
    }
}