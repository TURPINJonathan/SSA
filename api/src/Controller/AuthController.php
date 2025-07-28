<?php

namespace App\Controller;

use App\Entity\Agent;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AuthController extends AbstractController
{
	#[Route('/get-agent-from-token', name: 'app_auth_me', methods: ['GET'])]
	#[IsGranted('IS_AUTHENTICATED_FULLY')]
	public function me(EntityManagerInterface $em): JsonResponse
	{
		/** @var User $user */
		$user = $this->getUser();

		$agent = $em->getRepository(Agent::class)->findOneBy(['user' => $user]);

		if (!$agent) {
			return $this->json(['error' => 'Agent not found'], 404);
		}

		return $this->json([
			'agent' => [
				'id' => $agent->getId(),
				'codename' => $agent->getCodename(),
				'status' => $agent->getStatus()?->value ?? null,
				'roles' => $user->getRoles(),
			]
		]);
	}
}
