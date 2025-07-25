<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ApiResource]
class Message
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 255, type: Types::STRING)]
	#[Assert\NotBlank(message: 'Message title is required.')]
	#[Assert\Type(type: 'string')]
	#[Assert\Length(max: 255)]
	#[Assert\Length(min: 3, max: 255)]
	private ?string $title = null;

	#[ORM\Column(length: 255, type: Types::STRING)]
	#[Assert\NotBlank(message: 'Message body is required.')]
	#[Assert\Type(type: 'string')]
	#[Assert\Length(max: 255)]
	#[Assert\Length(min: 3, max: 255)]
	private ?string $body = null;

	#[ORM\ManyToOne(inversedBy: 'messages')]
	#[ORM\JoinColumn(nullable: true)]
	private ?User $sendBy = null;

	#[ORM\ManyToOne(inversedBy: 'receivedMessages')]
	#[ORM\JoinColumn(nullable: true)]
	private ?User $recipient = null;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private \DateTimeImmutable $createdAt;

	public function __construct()
	{
		$this->createdAt = new \DateTimeImmutable();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getTitle(): ?string
	{
		return $this->title;
	}

	public function setTitle(string $title): static
	{
		$this->title = $title;

		return $this;
	}

	public function getBody(): ?string
	{
		return $this->body;
	}

	public function setBody(string $body): static
	{
		$this->body = $body;

		return $this;
	}

	public function getSendBy(): ?User
	{
		return $this->sendBy;
	}

	public function setSendBy(?User $sendBy): static
	{
		$this->sendBy = $sendBy;

		return $this;
	}

	public function getRecipient(): ?User
	{
		return $this->recipient;
	}

	public function setRecipient(?User $recipient): static
	{
		$this->recipient = $recipient;

		return $this;
	}

	public function getCreatedAt(): ?\DateTimeImmutable
	{
		return $this->createdAt;
	}

	public function setCreatedAt(\DateTimeImmutable $createdAt): static
	{
		$this->createdAt = $createdAt;
		return $this;
	}
}
