<?php

namespace App\Card;

use App\Card\Probability;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Game21Win
{
    public function genericWin(Game21 $game, SessionInterface $session): bool
    {
        if ($game->getPlayerGamePoints() === 21 || $game->getBankGamePoints() > 21) {
            $game->setWinner('player');
            $game->getBetting()->clearBet('player', $session);
            return true;
        }

        if ($game->getPlayerGamePoints() > 21) {
            $game->setWinner('bank');
            $game->getBetting()->clearBet('bank', $session);
            return true;
        }
        return false;
    }

    public function dumbWin(Game21 $game, SessionInterface $session, string $who, string $gameMode): bool
    {
        if ($gameMode === 'dumb' && $who === 'bank' && $game->getBankGamePoints() >= 17) {
            if ($game->getBankGamePoints() >= $game->getPlayerGamePoints()) {
                $game->setWinner('bank');
                $game->getBetting()->clearBet('bank', $session);
                return true;
            }
            if ($game->getBankGamePoints() < $game->getPlayerGamePoints()) {
                $game->setWinner('player');
                $game->getBetting()->clearBet('player', $session);
                return true;
            }
        }
        return false;
    }

    public function smartWin(Game21 $game, SessionInterface $session, string $who, string $gameMode): bool
    {
        if ($gameMode === 'smart' && $who === 'bank') {
            //$inverseRisk = $game->getFatProbability('bank');

            $probability = new Probability();
            $inverseRisk = $probability->getFatProbability($game->getDrawCards(), $game->getDeck());

            if ($game->getBankGamePoints() >= $game->getPlayerGamePoints()) {
                $game->setWinner('bank');
                $game->getBetting()->clearBet('bank', $session);
                return true;
            }
            if ($inverseRisk < 30) {
                $game->setWinner('player');
                $game->getBetting()->clearBet('player', $session);
                return true;
            }
        }
        return false;
    }
}
