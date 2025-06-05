<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;

/**
 * Test class for class DeckOfCards
 */
class DeckOfCardsTestTest extends TestCase
{
    /**
     * Construct object and verify that the object is an instance of DeckOfCards. Also verify that
     * the deck consists of instances of Card.
     */
    public function testCreateObject(): void
    {
        $deck = new DeckOfCards();
        $this->assertInstanceOf("\App\Card\DeckOfCards", $deck);

        $cards = $deck->getDeck();
        foreach ($cards as $card) {
            $this->assertInstanceOf("\App\Card\Card", $card);
        }
    }

    /**
     * Construct a DeckOfCards object and verify that it has 52 cards.
     */
    public function testGetDeck(): void
    {
        $deck = new DeckOfCards();
        $cards = $deck->getDeck();

        $this->assertCount(52, $cards);

    }

    /**
     * Construct two DeckOfCards object, shuffle them and verify that they have 52 cards each.
     * Check that the shuffle function works.
     */
    public function testGetShuffledDeck(): void
    {
        $deck1 = new DeckOfCards();
        $shuffledCards1 = $deck1->shuffleAndGetDeck();

        $deck2 = new DeckOfCards();
        $shuffledCards2 = $deck2->shuffleAndGetDeck();

        $this->assertCount(52, $shuffledCards1);
        $this->assertCount(52, $shuffledCards2);

        //In extreme cases (completetely unlikely), this test could fail due to chance...
        $this->assertNotEquals($shuffledCards1, $shuffledCards2);

    }

    /**
     * Construct object and verify that an instance of Card is returned as well as the number
     * of the cards in the deck decreases by 1.
     */
    public function testDrawCardReturnsCardAndCountDecrease(): void
    {
        $deck = new DeckOfCards();

        $countBefore = count($deck->getDeck());

        $res = $deck->drawCard();
        $card = $res[0];
        $this->assertInstanceOf("\App\Card\Card", $card);

        $countAfter = $res[1];

        $this->assertEquals($countBefore, $countAfter + 1);
    }

    /**
     * Construct object and verify that null is returned when drawCard() is called on an empty deck.
     */
    public function testDrawCardEmptyDeck(): void
    {
        $deck = new DeckOfCards();

        // Empty the deck
        for ($i = 0; $i < 52; $i++) {
            $deck->drawCard();
        }

        // Check that drawCard returns null if deck is empty
        $isNull = $deck->drawCard();
        $this->assertNull($isNull);
    }

    /**
     * Construct object and verify that every value has 4 different colors in the deck
     */
    public function testGetNumberOfCardsNewDeck(): void
    {
        $deck = new DeckOfCards();
        $number = $deck->getNumberOfCards();

        $values = ['2', '3', '4', '5', '6', '7', '8', '9', '10', '♞', '♛', '♚', 'A'];

        foreach ($values as $value) {
            // Assert that the value is present in the array $number
            $this->assertArrayHasKey($value, $number);

            // Assert that there are 4 colors of each value.
            $this->assertEquals(4, $number[$value]);
        }
    }
}
