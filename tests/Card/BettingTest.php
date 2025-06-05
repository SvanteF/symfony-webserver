<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * Test class for class Betting
 */
class BettingTest extends TestCase
{
    /**
     * Construct a Betting object and check that the parameters are read correctly.
     */
    public function testBettingCreateAndCheckParameters(): void
    {
        $betting = new Betting();
        $playerFunds = $bankFunds = 100;
        $bet = 0;

        $this->assertSame($playerFunds, $betting->getPlayerFunds());
        $this->assertSame($bankFunds, $betting->getBankFunds());
        $this->assertSame($bet, $betting->getBet());
    }

    /**
     * Construct a Betting object, make a bet and ensure the correct bet is fetched with getBet()
     */
    public function testBettingOKBet(): void
    {
        $betting = new Betting();

        // Set a "random" bet
        $playersBet = 50;

        $betting->makeBet($playersBet);

        $this->assertSame($playersBet, $betting->getBet());
    }

    /**
     * Construct a Betting object and check that save to Session works by comparing the initated
     * object with the one saved in the session
     */
    public function testBettingSaveToSession(): void
    {
        $betting = new Betting();

        // Create a new session with Symfony
        $sessionStorage = new MockArraySessionStorage();
        $session = new Session($sessionStorage);

        // Call saveToSession
        $betting->saveToSession($session);

        // Check if the session contains the same object a was initiated
        $this->assertSame($betting, $session->get('betting'));
    }

    /**
    * Construct a Betting object...
    */
    public function testBettingClearBet(): void
    {
        $betting = new Betting();

        // Create a new session with Symfony
        $sessionStorage = new MockArraySessionStorage();
        $session = new Session($sessionStorage);

        // Test of scenario 1
        $bet = 99;
        $betting->makeBet($bet);

        // Get the funds before clearBet()
        $bankFundsBefore = $betting->getBankFunds();
        $playerFundsBefore = $betting->getPlayerFunds();

        // Check clearBet when the winner is 'player'
        $winner = 'player';
        $betting->clearBet($winner, $session);

        // Get the funds after clearBet()
        $bankFundsAfter = $betting->getBankFunds();
        $playerFundsAfter = $betting->getPlayerFunds();

        // Check that player´s funds increased by the value of $bet while the bank's funds decreased as much
        $this->assertSame($bankFundsBefore, $bankFundsAfter + $bet);
        $this->assertSame($playerFundsBefore, $playerFundsAfter - $bet);

        // Check that the value of the bet was reset to zero
        $this->assertSame(0, $betting->getBet());

        // Test of scenario 2
        $bet = 99;
        $betting->makeBet($bet);

        // Get the funds before clearBet()
        $bankFundsBefore = $betting->getBankFunds();
        $playerFundsBefore = $betting->getPlayerFunds();

        // Check clearBet when the winner is not 'player'
        $winner = 'notPlayer';
        $betting->clearBet($winner, $session);

        // Get the funds after clearBet()
        $bankFundsAfter = $betting->getBankFunds();
        $playerFundsAfter = $betting->getPlayerFunds();

        // Check that player´s funds decreased by the value of $bet while the bank's funds increased as much
        $this->assertSame($bankFundsBefore, $bankFundsAfter - $bet);
        $this->assertSame($playerFundsBefore, $playerFundsAfter + $bet);

        // Check that the value of the bet was reset to zero
        $this->assertSame(0, $betting->getBet());

    }
}
