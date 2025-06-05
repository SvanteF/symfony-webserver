<?php

namespace App\Card;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Betting
{
    private int $playerFunds;
    private int $bankFunds;
    private int $bet;


    public function __construct()
    {
        $this->playerFunds = 100;
        $this->bankFunds = 100;
        $this->bet = 0;
    }

    public function saveToSession(SessionInterface $session): void
    {
        $session->set('betting', $this);
    }

    public function makeBet(int $playersBet): void
    {
        $this->bet = $playersBet;
    }

    public function clearBet(string $winner, SessionInterface $session): void
    {
        if ($winner === 'player') {
            $this->bankFunds -= $this->bet;
            $this->playerFunds += $this->bet;
            $this->bet = 0;
            $this->saveToSession($session);
            return;
        }
        $this->bankFunds += $this->bet;
        $this->playerFunds -= $this->bet;
        $this->bet = 0;
        $this->saveToSession($session);
        return;
    }

    public function getPlayerFunds(): int
    {
        return $this->playerFunds;
    }

    public function getBankFunds(): int
    {
        return $this->bankFunds;
    }

    public function getBet(): int
    {
        return $this->bet;
    }
}
