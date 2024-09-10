<?php

namespace App\Repository;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\Entity\User;
use DateTime;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Sortie::class);
    }

//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findAllFiltered(
        User $user,
        bool $isAdmin,
        ?Campus $campus = null,
        ?string $recherche = null,
        ?DateTimeInterface $dateDebut = null,
        ?DateTimeInterface $dateFin = null,
        ?bool $organisateur = null,
        ?bool $inscrit = null,
        ?bool $nonInscrit = null,
        ?bool $passee = null,
    ) {
        $qb = $this->createQueryBuilder('s')
            ->where('s.dateSortie > :dateNow')
            ->setParameter('dateNow', date_add(new DateTime('now'), date_interval_create_from_date_string(" -1 month")))
        ;

        // filtre les sorties non publiées des autres utilisateurs si l'utilisateur n'est pas admin
        if (!$isAdmin) {
            $qb->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->andX(
                            's.organisateur = :user',
                            's.publiee = false'
                        ),
                        $qb->expr()->eq('s.publiee', 'true')
                    )
                )
                ->setParameter('user', $user)
            ;
        }

        if (isset($campus)) {
            $qb->andWhere('s.campus = :campus')
                ->setParameter('campus', $campus)
            ;
        }
        if (isset($recherche)) {
            $qb->andwhere(
                    $qb->expr()->like('LOWER(s.nom)', 'LOWER(:recherche)')
                )
                ->setParameter('recherche', '%' . strtolower($recherche) . '%')
            ;
        }
        if (isset($dateDebut)) {
            $qb->andwhere(':dateDebut < s.dateSortie')
                ->setParameter('dateDebut', $dateDebut)
            ;
        }
        if (isset($dateFin)) {
            $qb->andwhere(':dateFin > s.dateSortie')
                ->setParameter('dateFin', $dateFin)
            ;
        }
        if ($organisateur) {
            $qb->andwhere('s.organisateur = :user')
                ->setParameter('user', $user)
            ;
        }
        if ($inscrit) {
            $qb->andwhere(':user MEMBER OF s.participants')
                ->setParameter('user', $user)
            ;
        }
        if ($nonInscrit) {
            $qb->andwhere(':user NOT MEMBER OF s.participants')
                ->setParameter('user', $user)
            ;
        }
        if ($passee) {
            $qb->andwhere('DATE_ADD(s.dateSortie, s.duree, \'MINUTE\') < :now')
                ->setParameter('now', new DateTime('now'))
            ;
        }
        return $qb->getQuery()->getResult();
    }

}
