<?php

namespace App\Repository;

use App\Entity\Log;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Log>
 *
 * @method Log|null find($id, $lockMode = null, $lockVersion = null)
 * @method Log|null findOneBy(array $criteria, array $orderBy = null)
 * @method Log[]    findAll()
 * @method Log[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Log::class);
    }

//    /**
//     * @return Log[] Returns an array of Log objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Log
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function deleteOldLogs(): bool
    {
        $entityManager = $this->getEntityManager();
        $oneMinuteAgo = new \DateTime();
        $oneMinuteAgo->modify('-1 minute');

        $entityManager = $this->getEntityManager();
        $entityManager->beginTransaction();
        try {
                $oldRecords = $this->createQueryBuilder('r')
                ->where('r.createdAt < :oneMinuteAgo')
                ->setParameter('oneMinuteAgo', $oneMinuteAgo)
                ->getQuery()
                ->getResult();

                foreach ($oldRecords as $record) {
                    $entityManager->remove($record);
                }
                $entityManager->flush();

            $entityManager->commit();
            return true;
        } catch (\Exception $e) {
            $entityManager->rollback();
            throw $e;
        }
    }

    public function createLog(array $data): bool
    {
        $entityManager = $this->getEntityManager();
        $log = new Log();
        $log->setInput($data['input']);
        $log->setOutput($data['output']);
        $log->setSource('From '.$data['source']);
        $log->setCreatedAtAutomatically();

        $entityManager->persist($log);

        $entityManager->flush();

        return true;
    }
}
