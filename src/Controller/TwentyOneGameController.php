<?php

namespace App\Controller;

//use App\Card\Card;
//use App\Card\DeckOfCards;
//use App\Card\DeckWithJokers;
//use App\Card\Player;

use App\Card\Betting;
use App\Card\DeckOfCards;
use App\Card\Game21;
use App\Card\Probability;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class TwentyOneGameController extends AbstractController
{
    #[Route("/game", name: "game_start")]
    public function gameStart(): Response
    {
        return $this->render('game_start.html.twig');
    }

    #[Route("/game/doc", name: "game_doc")]
    public function gameDoc(): Response
    {
        return $this->render('game_doc.html.twig');
    }

    #[Route("/game/21/1", name: "game_21_1_get", methods: ["GET"])]
    public function game211Get(
        SessionInterface $session
    ): Response {
        $deck = new DeckOfCards();
        $session->set('deck', $deck);

        $betting = new Betting();

        $betting->saveToSession($session);

        return $this->render('game_21_1.html.twig', [
            'playerFunds' => $betting->getPlayerFunds(),
            'bankFunds' => $betting->getBankFunds(),
        ]);
    }

    #[Route("/game/21/2", name: "game_21_2_get", methods: ["GET"])]
    public function game212Get(
        Request $request,
        SessionInterface $session
    ): Response {

        //Save the player's choice of mode
        //$session->set('gameMode', $request->query->get('gameMode'));
        $gameMode = $request->query->get('gameMode') ?? $session->get('gameMode');
        $session->set('gameMode', $gameMode);

        $betting = $session->get('betting');

        //Added for smart game (no shuffle when deck exists)
        $deck = $session->get('deck') ?? null;

        $game21 = new Game21($betting, $deck);

        $game21->getNewCard('player');

        $game21->saveToSession($session);
        //Added for smart game
        $session->set('deck', $game21->getDeck());

        $probability = new Probability();
        $fatProbability = $probability->getFatProbability($game21->getDrawCards(), $game21->getDeck());

        return $this->render('game_21_2.html.twig', [
            'playersCards' => $game21->getPlayersCardsAsArray(),
            'playerPoints' => $game21->getPlayerGamePoints(),
            'bet' => $betting->getBet(),
            'playerFunds' => $betting->getPlayerFunds(),
            'bankFunds' => $betting->getBankFunds(),
            'betText' => 'Inget bet lagt än',
            //'probability' => $game21->getFatProbability(),
            'probability' => $fatProbability,
        ]);
    }

    #[Route("/game/21/2", name: "game_21_2_post", methods: ["POST"])]
    public function game212Post(
        Request $request,
        SessionInterface $session
    ): Response {
        $game21 = $session->get('game21');
        $betting = $session->get('betting');

        $probability = new Probability();
        $fatProbability = $probability->getFatProbability($game21->getDrawCards(), $game21->getDeck());

        if ($betting->getBet() == 0) {
            $bet = $request->request->get('playersBet');
            if ($bet > $betting->getPlayerFunds() || $bet > $betting->getBankFunds()) {
                $bet = 0;

                return $this->render('game_21_2.html.twig', [
                    'playersCards' => $game21->getPlayersCardsAsArray(),
                    'playerPoints' => $game21->getPlayerGamePoints(),
                    //'bet' => $betting->getBet(),
                    'bet' => $bet,
                    'bankFunds' => $betting->getBankFunds(),
                    'playerFunds' => $betting->getPlayerFunds(),
                    'betText' => 'Bet är för högt, det måste vara max saldot för bank eller player',
                    //'probability' => $game21->getFatProbability(),
                    'probability' => $fatProbability,
                    ]);
            }

            $betting->makeBet($bet);
        }

        $game21->getNewCard('player');

        $game21->saveToSession($session);
        $betting->saveToSession($session);

        $probability = new Probability();
        $fatProbability = $probability->getFatProbability($game21->getDrawCards(), $game21->getDeck());

        if ($game21->gameOver($session, 'player')) {
            if ($betting->getBankFunds() === 0 || $betting->getPlayerFunds() === 0) {
                return $this->render('game_over_betting.html.twig', [
                    'winner' => $game21->getWinner(),
                ]);
            }
            return $this->render('game_over.html.twig', [
                'playerPoints' => $game21->getPlayerGamePoints(),
                'playersCards' => $game21->getPlayersCardsAsArray(),
                'bankPoints' => $game21->getBankGamePoints(),
                'banksCards' => $game21->getBanksCardsAsArray(),
                'winner' => $game21->getWinner(),
                //'probability' => $game21->getFatProbability(),
                'probability' => $fatProbability,
            ]);
        }

        return $this->render('game_21_2.html.twig', [
         'playersCards' => $game21->getPlayersCardsAsArray(),
         'playerPoints' => $game21->getPlayerGamePoints(),
         'bet' => $betting->getBet(),
         'bankFunds' => $betting->getBankFunds(),
         'playerFunds' => $betting->getPlayerFunds(),
         //'probability' => $game21->getFatProbability(),
         'probability' => $fatProbability,
         ]);
    }

    #[Route("/game/21/3", name: "game_21_3_post", methods: ["POST"])]
    public function game213Post(
        SessionInterface $session
    ): Response {
        $betting = $session->get('betting');

        $game21 = $session->get('game21');

        $game21->getNewCard('bank');

        $game21->saveToSession($session);

        if ($game21->gameOver($session, 'bank')) {
            if ($betting->getBankFunds() === 0 || $betting->getPlayerFunds() === 0) {
                return $this->render('game_over_betting.html.twig', [
                    'winner' => $game21->getWinner(),
                ]);
            }
            return $this->render('game_over.html.twig', [
                'playerPoints' => $game21->getPlayerGamePoints(),
                'playersCards' => $game21->getPlayersCardsAsArray(),
                'bankPoints' => $game21->getBankGamePoints(),
                'banksCards' => $game21->getBanksCardsAsArray(),
                'winner' => $game21->getWinner(),
            ]);
        }

        return $this->render('game_21_3.html.twig', [
         'playersCards' => $game21->getPlayersCardsAsArray(),
         'playerPoints' => $game21->getPlayerGamePoints(),
         'banksCards' => $game21->getBanksCardsAsArray(),
         'bankPoints' => $game21->getBankGamePoints(),
         'bankFunds' => $betting->getBankFunds(),
         'playerFunds' => $betting->getPlayerFunds(),
         'bet' => $betting->getBet(),
         ]);
    }
}
