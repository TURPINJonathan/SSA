<?php

namespace App\EventListener;

use App\Entity\MissionResult;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: MissionResult::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: MissionResult::class)]
class MissionResultListener
{
	public function postPersist(MissionResult $missionResult, PostPersistEventArgs $event): void
	{
		$this->syncMissionStatus($missionResult, $event);
	}

	public function postUpdate(MissionResult $missionResult, PostUpdateEventArgs $event): void
	{
		$this->syncMissionStatus($missionResult, $event);
	}

	private function syncMissionStatus(MissionResult $missionResult, $event): void
	{
		$mission = $missionResult->getMission();
		$status = $missionResult->getStatus();

		if (($mission && $status) && ($mission->getStatus() !== $status)) {
			$mission->setStatus($status);

			$entityManager = $event->getObjectManager();
			$entityManager->persist($mission);
			$entityManager->flush();
		}
	}
}
