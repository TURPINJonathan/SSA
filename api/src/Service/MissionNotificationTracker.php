<?php

namespace App\Service;

use App\Entity\Mission;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;

class MissionNotificationTracker
{
	public function __construct(
		private EntityManagerInterface $entityManager
	) {}

	public function hasAlreadyNotified(Mission $mission): bool
	{
		$existingMessage = $this->entityManager->getRepository(Message::class)
			->createQueryBuilder('message')
			->andWhere('message.title = :title')
			->andWhere('message.body LIKE :body')
			->setParameter('title', 'ALERT: MISSION ACTIVE')
			->setParameter('body', '%' . $mission->getName() . '%')
			->setMaxResults(1)
			->getQuery()
			->getOneOrNullResult();

		return $existingMessage !== null;
	}
}
