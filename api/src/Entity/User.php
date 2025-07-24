<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'This email is already in use.')]
#[ApiResource(
	normalizationContext: ['groups' => ['user:read']],
	denormalizationContext: ['groups' => ['user:write']],
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	#[Groups(['user:read'])]
	private ?int $id = null;

	#[ORM\Column(length: 180)]
	#[Assert\NotBlank]
	#[Assert\Email]
	#[Groups(['user:read', 'user:write'])]
	private ?string $email = null;

	/**
	 * @var list<string> The user roles
	 */
	#[ORM\Column]
	#[Groups(['user:write'])]
	private array $roles = [];

	/**
	 * @var string The hashed password
	 */
	#[ORM\Column]
	#[Groups(['user:write'])]
	private ?string $password = null;

	#[ORM\Column(length: 150)]
	#[Groups(['user:write'])]
	private ?string $firstName = null;

	#[ORM\Column(length: 150)]
	#[Groups(['user:write'])]
	private ?string $lastName = null;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getEmail(): ?string
	{
		return $this->email;
	}

	public function setEmail(string $email): static
	{
		$this->email = $email;

		return $this;
	}

	/**
	 * A visual identifier that represents this user.
	 *
	 * @see UserInterface
	 */
	public function getUserIdentifier(): string
	{
		return (string) $this->email;
	}

	/**
	 * @see UserInterface
	 */
	public function getRoles(): array
	{
		$roles = $this->roles;
		// guarantee every user at least has ROLE_USER
		$roles[] = 'ROLE_USER';

		return array_unique($roles);
	}

	/**
	 * @param list<string> $roles
	 */
	public function setRoles(array $roles): static
	{
		$this->roles = $roles;

		return $this;
	}

	/**
	 * @see PasswordAuthenticatedUserInterface
	 */
	public function getPassword(): ?string
	{
		return $this->password;
	}

	public function setPassword(string $password): static
	{
		$this->password = $password;

		return $this;
	}

	/**
	 * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
	 */
	public function __serialize(): array
	{
		$data = (array) $this;
		$data["\0" . self::class . "\0password"] = hash('crc32c', $this->password);

		return $data;
	}

	#[\Deprecated]
	public function eraseCredentials(): void
	{
		// @deprecated, to be removed when upgrading to Symfony 8
	}

	public function getFirstName(): ?string
	{
		return $this->firstName;
	}

	public function setFirstName(string $firstName): static
	{
		$this->firstName = $firstName;

		return $this;
	}

	public function getLastName(): ?string
	{
		return $this->lastName;
	}

	public function setLastName(string $lastName): static
	{
		$this->lastName = $lastName;

		return $this;
	}
}
