<?php

namespace App\Component\Task;

use App\Component\Task\Context\ReportContext;
use DateTimeInterface;

interface ReportGeneratorInterface
{
    public function generate(DateTimeInterface $dateStart, DateTimeInterface $dateEnd);
}