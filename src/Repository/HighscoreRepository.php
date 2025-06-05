<?php

namespace App\Repository;

use App\Entity\Highscore;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Highscore>
 */
class HighscoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Highscore::class);
    }

    /**
     * Clear the table player_entity
     */
    public function resetHighscore(): void
    {
        $entityManager = $this->getEntityManager();

        $allHighscores = $this->findAll();
        foreach ($allHighscores as $highscore) {
            $entityManager->remove($highscore);
        }

        $entityManager->flush();
    }
}
