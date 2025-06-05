<?php

namespace App\Card;

class Player
{
    private string $name;

    /**
    * @var Card[]
    */
    private array $cardHand = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function giveCard(Card $card): void
    {
        $this->cardHand[] = $card;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
    * @return Card[]
    */
    public function getCardHand(): array
    {
        return $this->cardHand;
    }
}
