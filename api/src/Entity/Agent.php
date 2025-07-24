<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\AgentStatus;
use App\Repository\AgentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AgentRepository::class)]
#[ApiResource]
class Agent
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 255, type: Types::STRING)]
	#[Assert\NotBlank]
	#[Assert\Length(max: 255)]
	#[Assert\Type(type: 'string')]
	#[Assert\Length(min: 3, max: 50)]
	private ?string $codename = null;

	#[ORM\Column(type: Types::INTEGER)]
	#[Assert\NotBlank]
	#[Assert\Range(min: 0, max: 100)]
	#[Assert\Type(type: 'integer')]
	#[Assert\PositiveOrZero]
	#[Assert\LessThanOrEqual(value: 100)]
	#[Assert\GreaterThanOrEqual(value: 0)]
	#[Assert\Range(min: 0, max: 50)]
	private ?int $yearsOfExperience = null;

	#[ORM\Column(length: 255, enumType: AgentStatus::class, options: ['default' => AgentStatus::AVAILABLE])]
	#[Assert\NotBlank]
	#[Assert\Type(type: AgentStatus::class)]
	#[Assert\Valid]
	private ?AgentStatus $status = AgentStatus::AVAILABLE;

	#[ORM\Column(type: Types::DATE_MUTABLE)]
	#[Assert\NotBlank]
	#[Assert\Type(type: \DateTime::class)]
	private ?\DateTime $enrolementDate = null;

	#[ORM\OneToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(nullable: true, unique: true)]
	#[Assert\Type(type: User::class)]
	private ?User $user = null;

	#[ORM\ManyToOne(targetEntity: Country::class, inversedBy: 'agentsInfiltrated')]
	private ?Country $countryInfiltrated = null;

	/**
	 * @var Collection<int, Mission>
	 */
	#[ORM\ManyToMany(targetEntity: Mission::class, mappedBy: 'agents')]
	private Collection $currentMission;

	public function __construct()
	{
		$this->currentMission = new ArrayCollection();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getCodename(): ?string
	{
		return $this->codename;
	}

	public function setCodename(string $codename): static
	{
		$this->codename = $codename;

		return $this;
	}

	public function getYearsOfExperience(): ?int
	{
		return $this->yearsOfExperience;
	}

	public function setYearsOfExperience(int $yearsOfExperience): static
	{
		$this->yearsOfExperience = $yearsOfExperience;

		return $this;
	}

	public function getStatus(): ?AgentStatus
	{
		return $this->status;
	}

	public function setStatus(AgentStatus $status): self
	{
		$this->status = $status;

		return $this;
	}

	public function getEnrolementDate(): ?\DateTime
	{
		return $this->enrolementDate;
	}

	public function setEnrolementDate(\DateTime $enrolementDate): static
	{
		$this->enrolementDate = $enrolementDate;

		return $this;
	}

	public function getUser(): ?User
	{
		return $this->user;
	}

	public function setUser(?User $user): static
	{
		$this->user = $user;

		return $this;
	}

	public function getCountryInfiltrated(): ?Country
	{
		return $this->countryInfiltrated;
	}

	public function setCountryInfiltrated(?Country $countryInfiltrated): static
	{
		$this->countryInfiltrated = $countryInfiltrated;

		return $this;
	}

	/**
	 * @return Collection<int, Mission>
	 */
	public function getCurrentMission(): Collection
	{
		return $this->currentMission;
	}

	public function addCurrentMission(Mission $currentMission): static
	{
		if (!$this->currentMission->contains($currentMission)) {
			$this->currentMission->add($currentMission);
			$currentMission->addAgent($this);
		}

		return $this;
	}

	public function removeCurrentMission(Mission $currentMission): static
	{
		if ($this->currentMission->removeElement($currentMission)) {
			$currentMission->removeAgent($this);
		}

		return $this;
	}
}
