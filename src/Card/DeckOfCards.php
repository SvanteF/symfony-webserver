<?php

namespace App\Card;

/**
 * DeckOfCards is a composition containing one or more Card objects
 */
class DeckOfCards
{
    /**
    * @var Card[] $deck
    */
    protected array $deck = [];

    /**
     * Constructor that initilizes the deck with all 52 Card Objects
     */
    public function __construct()
    {
        $colors = ['♥', '♣', '♦', '♠'];
        $values = ['2', '3', '4', '5', '6', '7', '8', '9', '10', '♞', '♛', '♚', 'A'];

        foreach ($colors as $color) {
            foreach ($values as $value) {
                $this->deck[] = new Card($value, $color);
            }
        }
    }

    /**
     * Shuffle the existing deck and return it as an array of Card objects
     *
     * @return Card[]
     */

    public function shuffleAndGetDeck(): array
    {
        shuffle($this->deck);

        return $this->deck;
    }

    /**
     * Draw a random card from the deck. If the deck is empty, return null
     *
     * @return array{0: Card, 1: int}|null
     */
    public function drawCard(): ?array
    {
        if (count($this->deck) === 0) {
            return null;
        }

        $randomPos = array_rand($this->deck);
        $card = $this->deck[$randomPos];

        unset($this->deck[$randomPos]);
        $this->deck = array_values($this->deck);

        return [$card, count($this->deck)];
    }

    /**
     * Get the deck as an array of Cards
     *
     * @return Card[]
     */
    public function getDeck(): array
    {
        return $this->deck;
    }

    /**
     * Get the number of remaining cards sorted by value
     *
     * @return array<int|string,int>
     */
    public function getNumberOfCards(): array
    {
        $number = [
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
        ];
        foreach ($this->deck as $card) {
            $value = $card->getValue();
            $number[$value]++;
        }
        return $number;
    }
}
