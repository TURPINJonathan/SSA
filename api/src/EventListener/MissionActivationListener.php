<?php

namespace App\EventListener;

use App\Entity\Mission;
use App\Entity\Message;
use App\Repository\UserRepository;
use App\Service\MissionNotificationTracker;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;

#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postUpdate)]
class MissionActivationListener
{
	public function __construct(
		private UserRepository $userRepository,
		private LoggerInterface $logger,
		private MissionNotificationTracker $missionNotificationTracker
	) {}

	public function postPersist(PostPersistEventArgs $args): void
	{
		$this->checkMissionActivation($args);
	}

	public function postUpdate(PostUpdateEventArgs $args): void
	{
		$this->checkMissionActivation($args);
	}

	private function checkMissionActivation($args): void
	{
		$entity = $args->getObject();

		if (!$entity instanceof Mission) {
			return;
		}

		if (!$entity->isActive() || $entity->getStatus() !== null) {
			return;
		}

		$this->sendNotificationToAgents($entity, $args->getObjectManager());
	}

	private function sendNotificationToAgents(Mission $mission, $entityManager): void
	{
		$country = $mission->getCountry();
		if (!$country) {
			$this->logger->warning('Mission without defined country', ['mission_id' => $mission->getId()]);
			return;
		}

		$this->logger->info('Mission activation detected', [
			'mission_id' => $mission->getId(),
			'mission_name' => $mission->getName(),
			'country_id' => $country->getId(),
			'country_name' => $country->getName(),
			'is_active' => $mission->isActive(),
			'status' => $mission->getStatus()
		]);

		// Éviter les notifications en double
		if ($this->missionNotificationTracker->hasAlreadyNotified($mission)) {
			return;
		}

		// Récupérer directement les agents qui ne participent PAS à cette mission
		$recipientAgents = $this->userRepository->findAgentsNotInMission(
			$country->getId(),
			$mission->getId()
		);

		$this->logger->info('Found recipient agents', [
			'mission_id' => $mission->getId(),
			'country_id' => $country->getId(),
			'recipients_count' => count($recipientAgents),
			'recipients_ids' => array_map(fn($agent) => $agent->getId(), $recipientAgents)
		]);

		if (empty($recipientAgents)) {
			$this->logger->warning('No recipient agents found for mission notification');
			return;
		}

		// Créer et envoyer le message à chaque agent non-participant
		foreach ($recipientAgents as $agent) {
			$message = new Message();
			$message->setTitle('ALERT: MISSION ACTIVE');
			$message->setBody(sprintf(
				'ALERT: A new mission "%s" (danger level: %s) has started in your country (%s). Stay vigilant and listen for instructions.',
				$mission->getName(),
				$mission->getDanger()->value,
				$country->getName()
			));
			$message->setRecipient($agent);
			$message->setSendBy(null);

			$entityManager->persist($message);
		}

		$this->logger->info('Notifications sent for active mission', [
			'mission_id' => $mission->getId(),
			'recipients_count' => count($recipientAgents)
		]);

		$entityManager->flush();
	}
}
