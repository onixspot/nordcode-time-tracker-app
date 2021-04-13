<?php

namespace App\Component\Task;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;

interface ReportGeneratorInterface
{
    public function generate(DateTimeInterface $dateStart, DateTimeInterface $dateEnd): ArrayCollection;
}