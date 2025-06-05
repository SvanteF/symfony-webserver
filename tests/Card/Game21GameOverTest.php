<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * Test class for class Game21
 */
class Game21GameOverTest extends TestCase
{
    /**
     * @var \App\Card\Betting&\PHPUnit\Framework\MockObject\MockObject $betting
     */
    private \PHPUnit\Framework\MockObject\MockObject $betting;

    /**
     * @var \App\Card\DeckOfCards&\PHPUnit\Framework\MockObject\MockObject $deck
     */
    private \PHPUnit\Framework\MockObject\MockObject $deck;

    /**
     * Construct required objects to avoid replicated code.
     */
    protected function setUp(): void
    {
        $this->betting = $this->createMock(Betting::class);

        $this->deck = $this->createMock(DeckOfCards::class);
    }

    /**
     * Verify that Genereic win performs accodring to spec
     */
    public function testGame21GameOverGenericWin(): void
    {
        /**
         * @var SessionInterface&\PHPUnit\Framework\MockObject\MockObject $session
         */

        $session = $this->createMock(SessionInterface::class);
        $session->method('get')->with('gameMode')->willReturn('dumb');

        $this->betting->expects($this->exactly(3))
        ->method('clearBet')
        ->with($this->callback(function ($winner) {
            return in_array($winner, ['player', 'bank']);
        }), $this->equalTo($session));

        // Construct a hand of 21
        $card1 = new Card('♞', '♥');
        $card2 = new Card('10', '♥');

        // Construct a "fat hand"
        $card3 = new Card('♞', '♣');
        $card4 = new Card('♚', '♣');

        // Construct another "fat hand"
        $card5 = new Card('♞', '♦');
        $card6 = new Card('♚', '♦');

        $this->deck->method('drawCard')->willReturnOnConsecutiveCalls(
            [$card1, 51],
            [$card2, 50],
            [$card3, 49],
            [$card4, 48],
            [$card5, 47],
            [$card6, 46]
        );

        //In case of player gets 21
        $game = new Game21($this->betting, $this->deck);
        $game->getNewCard('player');
        $game->getNewCard('player');
        $game->gameOver($session, 'player');
        $this->assertEquals('player', $game->getWinner());

        //In case of bank becomes fat with 24 points
        $game = new Game21($this->betting, $this->deck);
        $game->getNewCard('bank');
        $game->getNewCard('bank');
        $game->gameOver($session, 'bank');
        $this->assertEquals('player', $game->getWinner());

        //In case of player becomes fat with 24 points
        $game = new Game21($this->betting, $this->deck);
        $game->getNewCard('player');
        $game->getNewCard('player');
        $game->gameOver($session, 'player');
        $this->assertEquals('bank', $game->getWinner());
    }

    /**
     * Verify that Dumb win performs according to spec
     */
    public function testGame21GameOverDumbWinBankHasHigherPoints(): void
    {
        /**
         * @var SessionInterface&\PHPUnit\Framework\MockObject\MockObject $session
         */

        $session = $this->createMock(SessionInterface::class);
        $session->method('get')->with('gameMode')->willReturn('dumb');

        $this->betting->expects($this->once())
        ->method('clearBet')
        ->with('bank', $session);

        // Construct a hand of 17
        $card1 = new Card('7', '♥');
        $card2 = new Card('10', '♥');

        $this->deck->method('drawCard')->willReturnOnConsecutiveCalls(
            [$card1, 51],
            [$card2, 50],
        );

        //In case of bank gets 17 points and the player 0
        $game = new Game21($this->betting, $this->deck);
        $game->getNewCard('bank');
        $game->getNewCard('bank');
        $game->gameOver($session, 'bank');
        $this->assertEquals('bank', $game->getWinner());
    }

    /**
     * Verify that Dumb win performs according to spec
     */
    public function testGame21GameOverDumbWinPlayerHasHigherPoints(): void
    {
        /**
             * @var SessionInterface&\PHPUnit\Framework\MockObject\MockObject $session
             */

        $session = $this->createMock(SessionInterface::class);
        $session->method('get')->with('gameMode')->willReturn('dumb');

        $this->betting->expects($this->once())
        ->method('clearBet')
        ->with('player', $session);

        // Construct a hand of 17
        $card1 = new Card('7', '♥');
        $card2 = new Card('10', '♥');

        // Construct a hand of 18
        $card3 = new Card('8', '♦');
        $card4 = new Card('10', '♦');

        $this->deck->method('drawCard')->willReturnOnConsecutiveCalls(
            [$card1, 51],
            [$card2, 50],
            [$card3, 49],
            [$card4, 48]
        );

        //In case of bank gets 17 points and the player 18
        $game = new Game21($this->betting, $this->deck);
        $game->getNewCard('bank');
        $game->getNewCard('bank');
        $game->getNewCard('player');
        $game->getNewCard('player');
        $game->gameOver($session, 'bank');
        $this->assertEquals('player', $game->getWinner());
    }

    /**
     * Verify that Dumb win performs according to spec
     */
    public function testGame21GameOverDumbWinDraw(): void
    {
        /**
             * @var SessionInterface&\PHPUnit\Framework\MockObject\MockObject $session
             */

        $session = $this->createMock(SessionInterface::class);
        $session->method('get')->with('gameMode')->willReturn('dumb');

        $this->betting->expects($this->once())
        ->method('clearBet')
        ->with('bank', $session);

        // Construct a hand of 18
        $card1 = new Card('8', '♥');
        $card2 = new Card('10', '♥');

        // Construct a hand of 18
        $card3 = new Card('8', '♦');
        $card4 = new Card('10', '♦');

        $this->deck->method('drawCard')->willReturnOnConsecutiveCalls(
            [$card1, 51],
            [$card2, 50],
            [$card3, 49],
            [$card4, 48]
        );

        //In case of bank gets 18 points and the player 18 - draw = bank wins
        $game = new Game21($this->betting, $this->deck);
        $game->getNewCard('bank');
        $game->getNewCard('bank');
        $game->getNewCard('player');
        $game->getNewCard('player');
        $game->gameOver($session, 'bank');
        $this->assertEquals('bank', $game->getWinner());
    }

    /**
     * Verify that Smart win performs according to spec
     */
    public function testGame21GameOverDumbWinBankHasHigherPointsOrDraw(): void
    {
        /**
             * @var SessionInterface&\PHPUnit\Framework\MockObject\MockObject $session
             */

        $session = $this->createMock(SessionInterface::class);
        $session->method('get')->with('gameMode')->willReturn('smart');

        $this->betting->expects($this->once())
        ->method('clearBet')
        ->with('bank', $session);

        // Construct a hand of 17
        $card1 = new Card('7', '♥');
        $card2 = new Card('♚', '♥');

        $this->deck->method('drawCard')->willReturnOnConsecutiveCalls(
            [$card1, 51],
            [$card2, 50],
        );

        //In case of bank has higher points than the player (in this case 20 vs. 0)
        $game = new Game21($this->betting, $this->deck);
        $game->getNewCard('bank');
        $game->getNewCard('bank');
        $game->gameOver($session, 'bank');
        $this->assertEquals('bank', $game->getWinner());
    }

    /**
     * Verify that Dumb win performs according to spec
     */
    public function testGame21GameOverSmartWinRiskTooHigh(): void
    {
        /**
             * @var SessionInterface&\PHPUnit\Framework\MockObject\MockObject $session
             */

        $session = $this->createMock(SessionInterface::class);
        $session->method('get')->with('gameMode')->willReturn('smart');

        $this->betting->expects($this->once())
        ->method('clearBet')
        ->with('player', $session);

        // Construct a hand of 19 -> bank
        $card1 = new Card('9', '♥');
        $card2 = new Card('10', '♥');

        // Construct a hand of 20 -> player
        $card3 = new Card('9', '♦');
        $card4 = new Card('♞', '♦');

        $this->deck->method('drawCard')->willReturnOnConsecutiveCalls(
            [$card1, 51],
            [$card2, 50],
            [$card3, 49],
            [$card4, 48]
        );

        $this->deck->method('getNumberOfCards')->willReturn([
            'A' => 4, '2' => 4, '3' => 4, '4' => 4, '5' => 4, '6' => 4, '7' => 4, '8' => 4, '9' => 4, '10' => 4, '♞' => 4, '♛' => 4, '♚' => 4
        ]);

        //In case of bank gets 19 points and the player 20, the risk should be too high for the bank to proceed
        $game = new Game21($this->betting, $this->deck);
        $game->getNewCard('bank');
        $game->getNewCard('bank');
        $game->getNewCard('player');
        $game->getNewCard('player');
        $game->gameOver($session, 'bank');
        $this->assertEquals('player', $game->getWinner());
    }

    /**
     * Verify that game continous when no gameOver conditions are met.
     */
    public function testGame21GameOverItsNotOver(): void
    {
        /**
             * @var SessionInterface&\PHPUnit\Framework\MockObject\MockObject $session
             */

        $session = $this->createMock(SessionInterface::class);
        $session->method('get')->with('gameMode')->willReturn('smart');

        $this->betting->expects($this->never())->method('clearBet');

        // Construct a hand of 5 -> bank
        $card1 = new Card('2', '♥');
        $card2 = new Card('3', '♥');

        // Construct a hand of 7 -> player
        $card3 = new Card('3', '♦');
        $card4 = new Card('4', '♦');

        $this->deck->method('drawCard')->willReturnOnConsecutiveCalls(
            [$card1, 51],
            [$card2, 50],
            [$card3, 49],
            [$card4, 48]
        );

        // Make sure there is a full deck so getFatProbability is low
        $this->deck->method('getNumberOfCards')->willReturn([
            'A' => 4,
            '2' => 4,
            '3' => 4,
            '4' => 4,
            '5' => 4,
            '6' => 4,
            '7' => 4,
            '8' => 4,
            '9' => 4,
            '10' => 4,
            '♞' => 4,
            '♛' => 4,
            '♚' => 4
        ]);

        //Bank gets 5 and the player 7
        $game = new Game21($this->betting, $this->deck);
        $game->getNewCard('bank');
        $game->getNewCard('bank');
        $game->getNewCard('player');
        $game->getNewCard('player');

        $this->assertFalse($game->gameOver($session, 'bank'));
        $this->assertEquals('', $game->getWinner());
    }
}
