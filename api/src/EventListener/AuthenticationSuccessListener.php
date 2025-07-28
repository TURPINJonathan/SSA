<?php

namespace App\EventListener;

use App\Entity\User;
use App\Repository\AgentRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AuthenticationSuccessListener implements EventSubscriberInterface
{
	public function __construct(
		private AgentRepository $agentRepository
	) {}

	public static function getSubscribedEvents(): array
	{
		return [
			Events::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
		];
	}

	public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
	{
		$data = $event->getData();
		$user = $event->getUser();

		if (!$user instanceof User) {
			return;
		}

		$agent = $this->agentRepository->findOneBy(['user' => $user]);

		$data['agent'] = [
			'id' => $agent?->getId(),
			'codename' => $agent?->getCodename(),
			'roles' => $user->getRoles(),
			'status' => $agent?->getStatus(),
		];

		$event->setData($data);
	}
}
