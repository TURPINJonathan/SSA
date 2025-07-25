<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Enum\MissionStatus;
use App\Repository\MissionResultRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MissionResultRepository::class)]
#[ApiResource(
	operations: [
		new GetCollection(
			security: "is_granted('ROLE_ADMIN')",
			securityMessage: 'Only admins can view the list of agents.'
		),
		new Get(
			security: "is_granted('ROLE_AGENT') or is_granted('ROLE_ADMIN')",
			securityMessage: 'Only admins can view agent details.'
		),
		new Post(
			security: "is_granted('ROLE_AGENT') or is_granted('ROLE_ADMIN')",
			securityMessage: 'Only admins can create agents.'
		),
		new Patch(
			security: "is_granted('ROLE_AGENT') or is_granted('ROLE_ADMIN')",
			securityMessage: 'Only agents or admins can update agent details.'
		),
		new Delete(
			security: "is_granted('ROLE_ADMIN')",
			securityMessage: 'Only admins can delete agents.'
		)
	]
)]
class MissionResult
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 255)]
	private ?string $summary = null;

	#[ORM\OneToOne(inversedBy: 'missionResult')]
	#[ORM\JoinColumn(nullable: false)]
	private ?Mission $mission = null;

	#[ORM\Column(length: 255, enumType: MissionStatus::class)]
	#[Assert\NotBlank(message: 'Mission status is required.')]
	#[Assert\Type(type: MissionStatus::class)]
	private ?MissionStatus $status = null;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getSummary(): ?string
	{
		return $this->summary;
	}

	public function setSummary(string $summary): static
	{
		$this->summary = $summary;

		return $this;
	}

	public function getMission(): ?Mission
	{
		return $this->mission;
	}

	public function setMission(?Mission $mission): static
	{
		$this->mission = $mission;

		return $this;
	}

	public function getStatus(): ?MissionStatus
	{
		return $this->status;
	}

	public function setStatus(MissionStatus $status): static
	{
		$this->status = $status;

		return $this;
	}
}
