<?php

namespace App\Controller;

use App\Adventure\Game;
use App\Entity\PlayerEntity;
use App\Entity\Highscore;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdventureGameController extends AbstractController
{
    /**
     * Start Adventure, render the projects start page
     */
    #[Route("/proj", name: "adventure_start")]
    public function adventureStart(
        SessionInterface $session
    ): Response {
        $previousName = $session->get('previousName', '');
        return $this->render('adventure/start.html.twig', [
            'previousName' => $previousName,
        ]);
    }

    /**
     * Start a new game and render the play page
     */
    #[Route("/proj/game/new", name: "adventure_play", methods: ["POST"])]
    public function gameNew(
        ManagerRegistry $doctrine,
        Request $request,
        SessionInterface $session
    ): Response {
        $name = (string) ($request->request->get('name'));
        $name = trim($name);

        if ($name == '') {
            $this->addFlash('warning', 'Du glömde ditt namn, vänligen skriv in det innan du börjar');
            return $this->redirectToRoute('adventure_start');
        }

        // Create a new PlayerEntitity with every new game
        $playerEntity = new PlayerEntity();
        $playerEntity->setName($name);

        $entityManager = $doctrine->getManager();
        $entityManager->persist($playerEntity);
        $entityManager->flush();

        // Start the clock
        $startTime = new DateTimeImmutable();

        $game = new Game($name);
        $session->set('previousName', $name);
        $session->set('player_id', $playerEntity->getId());
        $session->set('start_time', $startTime->getTimestamp());
        $session->set('Game', $game);

        $player = $game->getPlayer();

        return $this->render('adventure/play.html.twig', [
            'room' => $player->getCurrentRoom(),
            'player' => $player,
        ]);
    }

    /**
     * Read the game from the session and render the play page
     */
    #[Route("/proj/game", name: "adventure_game", methods: ["GET"])]
    public function gamePlay(
        SessionInterface $session
    ): Response {

        $game = $session->get('Game');

        if (!$game) {
            return $this->redirectToRoute('adventure_start');
        }

        $player = $game->getPlayer();

        return $this->render('adventure/play.html.twig', [
            'room' => $player->getCurrentRoom(),
            'player' => $player,
        ]);
    }

    /**
     * Move the player from one room to another of the valid rooms available
     */
    #[Route("/proj/game/move/{where}", name: "adventure_move")]
    public function gameMove(
        string $where,
        SessionInterface $session
    ): Response {
        $game = $session->get('Game');
        $player = $game->getPlayer();
        $currentRoom = $player->getCurrentRoom();
        $nextRoom = $currentRoom->getDoorTo($where);

        $player->setCurrentRoom($nextRoom);

        $session->set('Game', $game);

        return $this->render('adventure/play.html.twig', [
            'room' => $player->getCurrentRoom(),
            'player' => $player,
        ]);
    }

    /**
     * Collect a thing (key or laundry) from a closet or a room (if closetId equals null).
     */
    #[Route("/proj/game/collect/{thingId}/{closetId}", name: "adventure_collect", methods: ["POST"], defaults: ["closetId" => null])]
    public function gameCollect(
        int $thingId,
        ?int $closetId,
        SessionInterface $session
    ): Response {
        $game = $session->get('Game');
        $player = $game->getPlayer();
        $room = $player->getCurrentRoom();

        if ($closetId !== null) {
            $closet = $room->getClosetById($closetId);
            $thing = $closet->getThingById($thingId);
            if ($thing) {
                $player->collectThingFromCloset($closet, $thing);
            }
            $session->set('Game', $game);
            return $this->redirectToRoute('adventure_game');
        }

        $thing = $room->getThingById($thingId);
        if ($thing) {
            $player->collectThingFromRoom($thing);
        }
        $session->set('Game', $game);

        return $this->redirectToRoute('adventure_game');
    }

    /**
     * Unlock a closet
     */
    #[Route("/proj/game/unlock/{closetId}", name: "adventure_unlock", methods: ["POST"])]
    public function unlockCloset(
        int $closetId,
        Request $request,
        SessionInterface $session
    ): Response {
        $keyId = $request->request->get('keyId');

        $game = $session->get('Game');
        $player = $game->getPlayer();
        $room = $player->getCurrentRoom();
        $closet = $room->getClosetById($closetId);

        $success = false;
        if ($closet->isLocked()) {
            $success = $player->useKeyOnCloset($keyId, $closet);
        }

        if ($success) {
            $this->addFlash(
                'success',
                'Rätt nyckel! Garderoben är nu öppen'
            );
            $session->set('Game', $game);

            return $this->redirectToRoute('adventure_game');
        }

        $this->addFlash(
            'warning',
            'Det var fel nyckel, garderoben är fortfarande låst'
        );

        $session->set('Game', $game);

        return $this->redirectToRoute('adventure_game');
    }

    /**
     * Calculate duration and load game over
     */
    #[Route("/proj/game/over", name: "adventure_game_over")]
    public function gameOver(
        ManagerRegistry $doctrine,
        SessionInterface $session
    ): Response {
        // Calculate the duration of the game
        $startTimestamp = $session->get('start_time');
        $endTime = new DateTimeImmutable();
        $endTimestamp = $endTime->getTimestamp();
        $gameDuration = $endTimestamp - $startTimestamp;

        // Get PlayerEntity from session
        $playerId = $session->get('player_id');
        $playerEntity = $doctrine->getRepository(PlayerEntity::class)->find($playerId);

        // Create highscore
        $highscore = new Highscore();
        if ($playerEntity !== null) {
            $highscore->setPlayer($playerEntity);
        }
        $highscore->setScore($gameDuration);
        $highscore->setCreated(new DateTimeImmutable('now', new DateTimeZone('Europe/Stockholm')));

        $entityManager = $doctrine->getManager();
        $entityManager->persist($highscore);
        $entityManager->flush();


        $session->remove('Game');
        $session->remove('start_time');
        $session->remove('player_id');

        return $this->render('adventure/over.html.twig', [
            'game_duration' => $gameDuration,
        ]);
    }

    /**
     * Load about page
     */
    #[Route("/proj/about", name: "adventure_about")]
    public function adventureAbout(
    ): Response {
        return $this->render('adventure/about.html.twig');
    }

    /**
     * Load about database page
     */
    #[Route("/proj/about/database", name: "adventure_about_database")]
    public function adventureAboutDatabase(
    ): Response {
        return $this->render('adventure/database.html.twig');
    }

    /**
     * Load the quick solution page
     */
    #[Route("/proj/quick", name: "adventure_quick")]
    public function adventureQuick(): Response
    {
        return $this->render('adventure/quick.html.twig');
    }
}
