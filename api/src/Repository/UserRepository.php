<?php

namespace App\Repository;

use App\Entity\Agent;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, User::class);
	}

	/**
	 * Used to upgrade (rehash) the user's password automatically over time.
	 */
	public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
	{
		if (!$user instanceof User) {
			throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
		}

		$user->setPassword($newHashedPassword);
		$this->getEntityManager()->persist($user);
		$this->getEntityManager()->flush();
	}

	/**
	 * Get all agents who are not currently assigned to a specific mission in a given country.
	 *
	 * @param integer $countryId
	 * @param integer $missionId
	 * @return User[]
	 */
	public function findAgentsNotInMission(int $countryId, int $missionId): array
	{
		return $this->createQueryBuilder('u')
			->join(Agent::class, 'a', 'WITH', 'a.user = u.id')
			->join('a.countryInfiltrated', 'c')
			->leftJoin('a.currentMission', 'm', 'WITH', 'm.id = :missionId')
			->andWhere('c.id = :countryId')
			->andWhere('u.roles LIKE :role')
			->andWhere('m.id IS NULL')
			->setParameter('countryId', $countryId)
			->setParameter('missionId', $missionId)
			->setParameter('role', '%ROLE_AGENT%')
			->getQuery()
			->getResult();
	}
}
