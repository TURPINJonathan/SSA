<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\DangerLevel;
use App\Repository\CountryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
#[ApiResource]
#[UniqueEntity(fields: ['name'], message: 'This country name already exists.')]
class Country
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 255, type: Types::STRING)]
	#[Assert\NotBlank(message: 'Country name is required.')]
	#[Assert\Type(type: 'string')]
	private ?string $name = null;

	#[ORM\Column(length: 255, enumType: DangerLevel::class)]
	#[Assert\NotBlank(message: 'Danger level is required.')]
	private ?DangerLevel $danger = DangerLevel::LOW;

	#[ORM\OneToOne(targetEntity: Agent::class)]
	#[ORM\JoinColumn(nullable: true)]
	#[Assert\Type(type: Agent::class)]
	private ?Agent $cellLeader = null;

	#[ORM\Column(type: Types::INTEGER)]
	#[Assert\NotBlank(message: 'Number of agents is required.')]
	#[Assert\Type(type: 'integer')]
	#[Assert\PositiveOrZero(message: 'Number of agents must be zero or positive.')]
	private ?int $numberOfAgents = 0;

	/**
	 * @var Collection<int, Agent>
	 */
	#[ORM\OneToMany(targetEntity: Agent::class, mappedBy: 'countryInfiltrated')]
	private Collection $agentsInfiltrated;

	/**
	 * @var Collection<int, Mission>
	 */
	#[ORM\OneToMany(targetEntity: Mission::class, mappedBy: 'country')]
	private Collection $missions;

	public function __construct()
	{
		$this->agentsInfiltrated = new ArrayCollection();
		$this->missions = new ArrayCollection();
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

	public function setDanger(DangerLevel $danger): static
	{
		$this->danger = $danger;
		return $this;
	}

	public function getCellLeader(): ?Agent
	{
		return $this->cellLeader;
	}

	public function setCellLeader(?Agent $cellLeader): static
	{
		$this->cellLeader = $cellLeader;
		return $this;
	}

	public function getNumberOfAgents(): ?int
	{
		return $this->numberOfAgents;
	}

	public function setNumberOfAgents(int $numberOfAgents): static
	{
		$this->numberOfAgents = $numberOfAgents;
		return $this;
	}

	/**
	 * @return Collection<int, Agent>
	 */
	public function getAgentsInfiltrated(): Collection
	{
		return $this->agentsInfiltrated;
	}

	public function addAgentsInfiltrated(Agent $infiltrated): static
	{
		if (!$this->agentsInfiltrated->contains($infiltrated)) {
			$this->agentsInfiltrated->add($infiltrated);
			$infiltrated->setCountryInfiltrated($this);
		}

		return $this;
	}

	public function removeAgentsInfiltrated(Agent $infiltrated): static
	{
		if ($this->agentsInfiltrated->removeElement($infiltrated)) {
			// set the owning side to null (unless already changed)
			if ($infiltrated->getCountryInfiltrated() === $this) {
				$infiltrated->setCountryInfiltrated(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection<int, Mission>
	 */
	public function getMissions(): Collection
	{
		return $this->missions;
	}

	public function addMission(Mission $mission): static
	{
		if (!$this->missions->contains($mission)) {
			$this->missions->add($mission);
			$mission->setCountry($this);
		}

		return $this;
	}

	public function removeMission(Mission $mission): static
	{
		if ($this->missions->removeElement($mission)) {
			// set the owning side to null (unless already changed)
			if ($mission->getCountry() === $this) {
				$mission->setCountry(null);
			}
		}

		return $this;
	}

	public function getActiveMissions(): Collection
	{
		return $this->missions->filter(fn(Mission $mission) => $mission->isActive());
	}

	public function calculateDangerLevel(): DangerLevel
	{
		$activeMissions = $this->getActiveMissions();

		if ($activeMissions->isEmpty()) {
			return DangerLevel::LOW;
		}

		$dangerLevels = [];
		foreach ($activeMissions as $mission) {
			$dangerLevels[] = $mission->getDanger();
		}

		return DangerLevel::getHighest($dangerLevels);
	}

	public function updateDangerLevel(): self
	{
		$this->danger = $this->calculateDangerLevel();
		return $this;
	}

	public function getCalculatedDangerLevel(): DangerLevel
	{
		return $this->calculateDangerLevel();
	}
}
