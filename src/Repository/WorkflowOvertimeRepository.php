<?php

namespace App\Repository;

use App\Entity\WorkflowOvertime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorkflowOvertime>
 */
class WorkflowOvertimeRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, WorkflowOvertime::class);
	}
}
