<?php
/**
 * @author Bocasay jul
 * Date : 30/12/2023
 */

namespace App\Repository;

use App\Entity\Contact;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        if (!$entity->getId()) {
            $this->getEntityManager()->persist($entity);
        }

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

    public function getEntityManagerIn(): EntityManagerInterface
    {
        return $this->getEntityManager();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function loadUserByEmailOrUsername(string $emailOrUsername)
    {
        $contact = $this->getEntityManager()->getRepository(Contact::class)->findOneBy(['email' => $emailOrUsername]);
        if (!empty($contact)) {
            return $this->createQueryBuilder('u')
                ->where('u.username = :emailOrUsername AND u.isEnabled = :enable')
                ->orWhere('u.contact = :contact AND u.isEnabled = :enable')
                ->setParameters([
                    'emailOrUsername' => $emailOrUsername,
                    'contact' => $contact,
                    'enable' => true,
                ])->getQuery()->getSingleResult();
        }

        return $this->createQueryBuilder('u')
            ->where('u.username = :emailOrUsername AND u.isEnabled = :enable')
            ->setParameters([
                'emailOrUsername' => $emailOrUsername,
                'enable' => true,
            ])->getQuery()->getSingleResult();
    }

    /**
     * @throws NonUniqueResultException|NoResultException
     */
    public function loadUserByRole(?string $role): float|bool|int|string|null
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->andWhere('CONTAINS(TO_JSONB(u.roles), :role) = TRUE')
            ->setParameter('role', '["'.$role.'"]')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findAllUsers(): Query
    {
        return $this->createQueryBuilder('u')
            ->getQuery();
    }
}
