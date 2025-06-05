<?php

namespace App\Controller;

use App\Entity\Highscore;
use App\Repository\PlayerRepository;
use App\Repository\HighscoreRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdventureDatabaseController extends AbstractController
{
    /**
     * Delete the database
     */
    #[Route('/proj/entity/delete', name: 'proj_reset', methods: ["POST"])]
    public function resetDatabase(
        PlayerRepository $playerRepository,
        HighscoreRepository $highscoreRepository
    ): Response {
        $playerRepository->resetPlayer();
        $highscoreRepository->resetHighscore();

        $this->addFlash('success', 'Databasen har återställts');


        return $this->redirectToRoute('adventure_about_database');
    }

    /**
     * Load the highscore page
     */
    #[Route("/proj/highscore", name: "adventure_highscore")]
    public function adventureHighscore(
        ManagerRegistry $doctrine
    ): Response {

        $entityManager = $doctrine->getManager();

        // Sort results ascending on score and limit to 10
        $highscores = $entityManager->getRepository(Highscore::class)->findBy([], ['score' => 'ASC'], 10);

        return $this->render('adventure/highscore.html.twig', [
            'highscores' => $highscores,
        ]);
    }

}
