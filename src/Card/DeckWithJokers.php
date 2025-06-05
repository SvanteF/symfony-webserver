<?php

namespace App\Card;

class DeckWithJokers extends DeckOfCards
{
    public function __construct()
    {
        parent::__construct();

        $this->deck[] = new Card('🃟', 'Black');
        $this->deck[] = new Card('🃟', 'Red');
    }
}
