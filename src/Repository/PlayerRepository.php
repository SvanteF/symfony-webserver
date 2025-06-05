<?php

namespace App\Repository;

use App\Entity\PlayerEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlayerEntity>
 */
class PlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerEntity::class);
    }

    /**
     * Clear the table player_entity
     */
    public function resetPlayer(): void
    {
        $entityManager = $this->getEntityManager();

        $allPlayers = $this->findAll();
        foreach ($allPlayers as $player) {
            $entityManager->remove($player);
        }

        $entityManager->flush();
    }

}
