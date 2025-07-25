<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Agent;
use App\Entity\User;
use App\Enum\AgentStatus;
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
class CreateAgentCommand extends Command
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

		$lastAgent = $this->entityManager->getRepository(Agent::class)
			->createQueryBuilder('a')
			->orderBy('a.id', 'DESC')
			->setMaxResults(1)
			->getQuery()
			->getOneOrNullResult();

		$nextId = $lastAgent ? $lastAgent->getId() + 1 : 1;
		$defaultCodename = '00' . $nextId;

		$agentCodename = $io->ask(
			"Agent codename [default: {$defaultCodename}]",
			$defaultCodename,
			function ($codename) {
				if (!empty($codename)) {
					$existingAgent = $this->entityManager->getRepository(Agent::class)
						->findOneBy(['codename' => $codename]);
					if ($existingAgent) {
						throw new \RuntimeException('This codename already exists. Please choose another one.');
					}
				}
				return $codename;
			}
		);

		$yearsOfExperience = $io->ask('Years of experience', '0', function ($years) {
			if (!is_numeric($years) || $years < 0) {
				throw new \RuntimeException('Years of experience must be a non-negative number.');
			}
			return (int)$years;
		});

		$agentStatus = $io->choice(
			'Agent status (required)',
			array_map(fn($status) => $status->value, AgentStatus::cases()),
			AgentStatus::AVAILABLE->value
		);

		$defaultDate = (new \DateTime())->format('Y-m-d');

		$agentEnrolementDate = $io->ask(
			"Agent enrollment date (YYYY-MM-DD)",
			$defaultDate,
			function ($date) {
				$dateTime = \DateTime::createFromFormat('Y-m-d', $date);
				if (!$dateTime) {
					throw new \RuntimeException('Invalid date format. Use YYYY-MM-DD.');
				}
				return $dateTime;
			}
		);

		$io->section('Mentor assignment');

		$existingAgents = $this->entityManager->getRepository(Agent::class)->findAll();

		$mentors = [];
		if (!empty($existingAgents)) {
			$mentorChoices = [];
			foreach ($existingAgents as $agent) {
				$displayName = $agent->getCodename() ?: 'Agent without codename';
				$mentorChoices[$displayName] = $agent->getId();
			}

			$io->note([
				'Multiple selection instructions:',
				'• Separate choices with commas (e.g., "0,1,2")',
				'• Use ranges with dashes (e.g., "0-2" for options 0, 1, and 2)',
				'• Mix both formats (e.g., "0,2-4,6")',
				'• Press Enter without typing anything to skip mentor assignment'
			]);

			$selectedMentorKeys = $io->choice(
				'Select mentors for this agent (optional, multiple selection allowed)',
				array_keys($mentorChoices),
				null,
				true
			);

			foreach ($selectedMentorKeys as $mentorKey) {
				$mentorAgent = $this->entityManager->getRepository(Agent::class)
					->find($mentorChoices[$mentorKey]);
				if ($mentorAgent) {
					$mentors[] = $mentorAgent;
				}
			}
		} else {
			$io->note('No existing agents found. No mentors will be assigned.');
		}

		$io->section('Credentialing selection');
		$availableRoles = [
			'Administrator' => 'ROLE_ADMIN',
			'Agent' => 'ROLE_AGENT'
		];

		$selectedRole = $io->choice(
			'Select role',
			array_keys($availableRoles),
			'Agent' // Valeur par défaut
		);

		$userRoles = ['ROLE_USER', $availableRoles[$selectedRole]];

		// Affichage du mentor sélectionné
		$mentorNames = !empty($mentors) ?
			implode(', ', array_map(fn($m) => $m->getCodename() ?: 'Agent without codename', $mentors)) :
			'None';

		$io->section('Summary');
		$io->table(
			['Field', 'Value'],
			[
				['Email', $email],
				['First name', $firstName],
				['Last name', $lastName],
				['Codename', $agentCodename ?: 'None'],
				['Years of experience', $yearsOfExperience],
				['Status', $agentStatus],
				['Enrollment date', $agentEnrolementDate->format('Y-m-d')],
				['Mentor', $mentorNames],
				['Role', $selectedRole],
			]
		);

		if (!$io->confirm('Confirm agent creation?', true)) {
			$io->info('Creation cancelled.');
			return Command::SUCCESS;
		}

		// Créer le User
		$user = new User();
		$user->setEmail($email);
		$user->setFirstName($firstName);
		$user->setLastName($lastName);
		$user->setRoles($userRoles);

		$hashedPassword = $this->passwordHasher->hashPassword($user, $password);
		$user->setPassword($hashedPassword);

		$this->entityManager->persist($user);

		// Créer l'entité Agent
		$agent = new Agent();
		$agent->setCodename($agentCodename);
		$agent->setYearsOfExperience($yearsOfExperience);
		$agent->setStatus(AgentStatus::from($agentStatus));
		$agent->setEnrolementDate($agentEnrolementDate);
		$agent->setUser($user);

		// Assigner le mentor (UN SEUL)
		foreach ($mentors as $mentor) {
			$agent->addMentee($mentor);
		}

		$this->entityManager->persist($agent);
		$this->entityManager->flush();

		$io->success([
			'Agent created successfully!',
			'Email: ' . $email,
			'Name: ' . $firstName . ' ' . $lastName,
			'Codename: ' . ($agentCodename ?: 'None'),
			'Mentor: ' . $mentorNames,
			'Role: ' . $selectedRole,
		]);

		return Command::SUCCESS;
	}
}
