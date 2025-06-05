<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * Test class for class Game21
 */
class Game21Test extends TestCase
{
    private Game21 $game21;

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

        $this->game21 = new Game21($this->betting, $this->deck);
    }

    /**
     * Verify instance of class Game21
     */
    public function testGame21Construct(): void
    {
        // Test with existing deck
        $this->assertInstanceOf("App\Card\Game21", $this->game21);

        // Test with non existing deck
        $game21 = new Game21($this->betting, null);
        $this->assertInstanceOf("App\Card\Game21", $game21);

    }

    /**
     * Verify that save to Session works by comparing the initated
     * object with the one saved in the session
     */
    public function testGame21SaveToSession(): void
    {
        // Create a new session with Symfony
        $sessionStorage = new MockArraySessionStorage();
        $session = new Session($sessionStorage);

        // Call saveToSession
        $this->game21->saveToSession($session);

        // Check if the session contains the same object a was initiated
        $this->assertSame($this->game21, $session->get('game21'));
    }

    /**
     * Verify that the player and bank get 1 new card with getNewCard
     */
    public function testGame21GetNewCard(): void
    {
        // Construct a Card
        $card = new Card('2', '♥');

        // Mock that one card is drawn from the full deck
        $this->deck->method('drawCard')->willReturn([$card, 51]);

        // Check getNewCard for player
        $this->game21->getNewCard('player');
        $drawCards = $this->game21->getDrawCards();
        $this->assertCount(1, $drawCards);
        $this->assertSame('2', $drawCards[0]->getValue());
        $this->assertSame('♥', $drawCards[0]->getColor());
        $this->assertEquals(2, $this->game21->getPlayerGamePoints());

        // Check getNewCard for bank
        $this->game21->getNewCard('bank');
        $drawCards = $this->game21->getBankCards();
        $this->assertCount(1, $drawCards);
        $this->assertSame('2', $drawCards[0]->getValue());
        $this->assertSame('♥', $drawCards[0]->getColor());
        $this->assertEquals(2, $this->game21->getBankGamePoints());
    }

    /**
     * Verify that a card is drawn even if the deck is initially empty
     */
    public function testGame21GetNewCardWhenDeckIsEmpty(): void
    {
        // Mock an empty deck
        $this->deck->method('getNumberOfCards')->willReturn([
            '2' => 0,
            '3' => 0,
            '4' => 0,
            '5' => 0,
            '6' => 0,
            '7' => 0,
            '8' => 0,
            '9' => 0,
            '10' => 0,
            '♞' => 0,
            '♛' => 0,
            '♚' => 0,
            'A' => 0
        ]);

        // Test for player
        $this->game21->getNewCard('player');
        $drawCards = $this->game21->getDrawCards();

        // Check that a card was drawn by the player despite the deck initiallt being empty
        $this->assertCount(1, $drawCards);

    }

    /**
     * Verify that in case of an Ace, count as 14 if possible.
     */
    public function testGame21GetNewCardAndNowWithAnAce(): void
    {

        // Construct a Card with an ace
        $card = new Card('A', '♥');

        // Mock that one card is drawn from the full deck
        $this->deck->method('drawCard')->willReturn([$card, 51]);

        // Check getNewCard for player
        $this->game21->getNewCard('player');
        $drawCards = $this->game21->getDrawCards();
        $this->assertCount(1, $drawCards);
        $this->assertSame('A', $drawCards[0]->getValue());
        $this->assertSame('♥', $drawCards[0]->getColor());
        $this->assertEquals(14, $this->game21->getPlayerGamePoints());

        // Check getNewCard for bank
        $this->game21->getNewCard('bank');
        $drawCards = $this->game21->getDrawCards();
        $this->assertCount(1, $drawCards);
        $this->assertSame('A', $drawCards[0]->getValue());
        $this->assertSame('♥', $drawCards[0]->getColor());
        $this->assertEquals(14, $this->game21->getPlayerGamePoints());
    }

    /**
     * Verify that a hand of cards (in this case 3 cards) is correctly fetched as an array of strings
     */
    public function testGame21CardsAsArray(): void
    {
        // Construct a few cards
        $card1 = new Card('A', '♥');
        $card2 = new Card('3', '♣');
        $card3 = new Card('3', '♥');

        $this->deck->method('drawCard')->willReturnOnConsecutiveCalls(
            [$card1, 51],
            [$card2, 50],
            [$card3, 49],
            [$card1, 51],
            [$card2, 50],
            [$card3, 49]
        );

        for ($i = 0; $i < 3; $i++) {
            $this->game21->getNewCard('player');
        }
        $resultPlayer = $this->game21->getPlayersCardsAsArray();

        for ($i = 0; $i < 3; $i++) {
            $this->game21->getNewCard('bank');
        }
        $resultBank = $this->game21->getBanksCardsAsArray();


        $this->assertSame(['[♥ A]', '[♣ 3]', '[♥ 3]'], $resultPlayer);
        $this->assertSame(['[♥ A]', '[♣ 3]', '[♥ 3]'], $resultBank);
    }

    /**
     * Verify that the last conditions for getFatProbability are met
     */
    public function testGame21GetFatProbabilityPlayer(): void
    {
        $card1 = new Card('3', '♥');
        $card2 = new Card('4', '♣');

        $this->deck->method('drawCard')->willReturnOnConsecutiveCalls([$card1, 51], [$card2, 50]);

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

        $game = new Game21($this->betting, $this->deck);
        $game->getNewCard('player');
        $game->getNewCard('player');
        $this->assertSame(100.0, (new Probability())->getFatProbability($game->getDrawCards(), $game->getDeck()));

    }

    /**
     * Verify that a deck of cards is fetched
     */

    public function testGame21GetDeckOfCards(): void
    {
        $this->game21->getDeck();

        $this->assertInstanceOf("App\Card\DeckOfCards", $this->deck);
    }
}
