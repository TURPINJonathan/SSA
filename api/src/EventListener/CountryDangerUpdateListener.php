<?php

namespace App\EventListener;

use App\Entity\Mission;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postUpdate)]
#[AsDoctrineListener(event: Events::preRemove)]
class CountryDangerUpdateListener
{
	public function postPersist(PostPersistEventArgs $args): void
	{
		$this->updateCountryDanger($args);
	}

	public function postUpdate(PostUpdateEventArgs $args): void
	{
		$this->updateCountryDanger($args);
	}

	public function preRemove(PreRemoveEventArgs $args): void
	{
		$this->updateCountryDanger($args);
	}

	private function updateCountryDanger($args): void
	{
		$entity = $args->getObject();

		if (!$entity instanceof Mission) {
			return;
		}

		$country = $entity->getCountry();
		if (!$country) {
			return;
		}

		$country->updateDangerLevel();

		$entityManager = $args->getObjectManager();
		$entityManager->persist($country);
		$entityManager->flush();
	}
}
