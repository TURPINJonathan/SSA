<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\DangerLevel;
use App\Enum\MissionStatus;
use App\Repository\MissionRepository;
use App\Validator\Constraints\AgentsInfiltrationConstraint;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MissionRepository::class)]
#[ApiResource]
#[AgentsInfiltrationConstraint]
class Mission
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 255, type: Types::STRING)]
	#[Assert\NotBlank(message: 'Mission name is required.')]
	#[Assert\Type(type: 'string')]
	private ?string $name = null;

	#[ORM\Column(length: 255, type: Types::STRING, enumType: DangerLevel::class)]
	#[Assert\NotBlank(message: 'Danger level is required.')]
	#[Assert\Type(type: DangerLevel::class)]
	private ?DangerLevel $danger = DangerLevel::LOW;

	#[ORM\Column(length: 255, nullable: true, enumType: MissionStatus::class)]
	#[Assert\Type(type: MissionStatus::class)]
	private ?MissionStatus $status = null;

	#[ORM\Column(length: 255, nullable: true, type: Types::STRING)]
	#[Assert\Type(type: 'string')]
	#[Assert\Length(max: 255)]
	#[Assert\Length(min: 3, max: 255)]
	private ?string $description = null;

	#[ORM\Column(length: 255, nullable: true, type: Types::STRING)]
	#[Assert\Type(type: 'string')]
	#[Assert\Length(max: 255)]
	#[Assert\Length(min: 3, max: 255)]
	private ?string $objectives = null;

	#[ORM\Column(type: Types::DATE_MUTABLE)]
	#[Assert\NotBlank(message: 'Start date is required.')]
	#[Assert\Type(type: \DateTime::class)]
	private ?\DateTime $startDate = null;

	#[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
	#[Assert\Type(type: \DateTime::class)]
	private ?\DateTime $endDate = null;

	/**
	 * @var Collection<int, Agent>
	 */
	#[ORM\ManyToMany(targetEntity: Agent::class, inversedBy: 'currentMission')]
	private Collection $agents;

	#[ORM\OneToOne(mappedBy: 'mission', cascade: ['persist', 'remove'])]
	private ?MissionResult $missionResult = null;

	#[ORM\ManyToOne(inversedBy: 'missions')]
	#[ORM\JoinColumn(nullable: false)]
	private ?Country $country = null;

	public function __construct()
	{
		$this->agents = new ArrayCollection();
		$this->startDate = new \DateTime();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): static
	{
		$this->name = $name;

		return $this;
	}

	public function getDanger(): ?DangerLevel
	{
		return $this->danger;
	}

	public function setDanger(DangerLevel $danger): self
	{
		$this->danger = $danger;

		return $this;
	}

	public function getStatus(): ?MissionStatus
	{
		return $this->status;
	}

	public function setStatus(MissionStatus $status): self
	{
		$this->status = $status;

		return $this;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(?string $description): static
	{
		$this->description = $description;

		return $this;
	}

	public function getObjectives(): ?string
	{
		return $this->objectives;
	}

	public function setObjectives(?string $objectives): static
	{
		$this->objectives = $objectives;

		return $this;
	}

	public function getStartDate(): ?\DateTime
	{
		return $this->startDate;
	}

	public function setStartDate(\DateTime $startDate): static
	{
		$this->startDate = $startDate;

		return $this;
	}

	public function getEndDate(): ?\DateTime
	{
		return $this->endDate;
	}

	public function setEndDate(?\DateTime $endDate): static
	{
		$this->endDate = $endDate;

		return $this;
	}

	/**
	 * @return Collection<int, Agent>
	 */
	public function getAgents(): Collection
	{
		return $this->agents;
	}

	public function addAgent(Agent $agent): static
	{
		if (!$this->agents->contains($agent)) {
			$this->agents->add($agent);
		}

		return $this;
	}

	public function removeAgent(Agent $agent): static
	{
		$this->agents->removeElement($agent);

		return $this;
	}

	public function getMissionResult(): ?MissionResult
	{
		return $this->missionResult;
	}

	public function setMissionResult(?MissionResult $missionResult): static
	{
		// unset the owning side of the relation if necessary
		if ($missionResult === null && $this->missionResult !== null) {
			$this->missionResult->setMission(null);
		}

		// set the owning side of the relation if necessary
		if ($missionResult !== null && $missionResult->getMission() !== $this) {
			$missionResult->setMission($this);
		}

		$this->missionResult = $missionResult;

		return $this;
	}

	public function getCountry(): ?Country
	{
		return $this->country;
	}

	public function setCountry(?Country $country): static
	{
		$this->country = $country;

		return $this;
	}

	/**
	 * A mission is considered active if:
	 * - it has no status set (null)
	 * - the start date is in the past or today
	 * - the end date is either null or in the future (including today)
	 */
	public function isActive(): bool
	{
		return $this->status === null && (
			$this->startDate <= new \DateTime() &&
			($this->endDate === null || $this->endDate >= new \DateTime()));
	}
}
