<?php

namespace App\Repository;

use App\Entity\Addresse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Addresse>
 *
 * @method Addresse|null find($id, $lockMode = null, $lockVersion = null)
 * @method Addresse|null findOneBy(array $criteria, array $orderBy = null)
 * @method Addresse[]    findAll()
 * @method Addresse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AddresseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Addresse::class);
    }
}
