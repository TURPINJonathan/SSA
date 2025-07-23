<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
	name: 'create-agent',
	description: 'Create an agent for SSA',
	aliases: ['cr:ag'],
)]
class CreateAdminAgentCommand extends Command
{
	public function __construct(
		private EntityManagerInterface $entityManager,
		private UserPasswordHasherInterface $passwordHasher
	) {
		parent::__construct();
	}

	protected function configure(): void
	{
		$this->setDescription('Create an agent for SSA');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$io = new SymfonyStyle($input, $output);

		$io->title('Creating SSA Agent');
		$io->note('This command will create an agent.');

		$email = $io->ask('Agent email', null, function ($email) {
			if (empty($email)) {
				throw new \RuntimeException('Email cannot be empty.');
			}
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				throw new \RuntimeException('Email is not valid.');
			}
			return $email;
		});

		$existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
		if ($existingUser) {
			$io->error('An agent with this email already exists!');
			return Command::FAILURE;
		}

		$password = $io->askHidden('Password', function ($password) {
			if (empty($password)) {
				throw new \RuntimeException('Password cannot be empty.');
			}
			if (strlen($password) < 6) {
				throw new \RuntimeException('Password must be at least 6 characters long.');
			}
			return $password;
		});

		$firstName = $io->ask('First name', null, function ($firstName) {
			if (empty($firstName)) {
				throw new \RuntimeException('First name cannot be empty.');
			}
			return $firstName;
		});

		$lastName = $io->ask('Last name', null, function ($lastName) {
			if (empty($lastName)) {
				throw new \RuntimeException('Last name cannot be empty.');
			}
			return $lastName;
		});

		$io->section('Credentialing selection');
		$availableRoles = [
			'Administrator' => 'ROLE_ADMIN',
			'Agent' => 'ROLE_AGENT'
		];

		$selectedRoles = $io->choice(
			'Select roles (separate multiple choices with comma)',
			array_keys($availableRoles),
			null,
			true
		);

		$userRoles = ['ROLE_USER'];
		foreach ($selectedRoles as $roleName) {
			$userRoles[] = $availableRoles[$roleName];
		}

		$io->section('Summary');
		$io->table(
			['Field', 'Value'],
			[
				['Email', $email],
				['First name', $firstName],
				['Last name', $lastName],
				['Credentialing', implode(', ', $selectedRoles)],
			]
		);

		if (!$io->confirm('Confirm agent creation?', true)) {
			$io->info('Creation cancelled.');
			return Command::SUCCESS;
		}

		$user = new User();
		$user->setEmail($email);

		$user->setFirstName($firstName);
		$user->setLastName($lastName);
		$user->setRoles($userRoles);

		$hashedPassword = $this->passwordHasher->hashPassword($user, $password);
		$user->setPassword($hashedPassword);

		$this->entityManager->persist($user);
		$this->entityManager->flush();

		$io->success([
			'Agent created successfully!',
			'Email: ' . $email,
			'Name: ' . $firstName . ' ' . $lastName,
			'Credentialing: ' . implode(', ', $selectedRoles),
		]);

		return Command::SUCCESS;
	}
}
