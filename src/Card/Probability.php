<?php

namespace App\Card;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Probability
{
    /**
     * @param Card[] $currentHand
     */
    public function getFatProbability(array $currentHand, DeckOfCards $deck): float
    {
        //Setting A to 1 since we only want to avoid "getting fat"
        $pointsTable = [
            'A' => 1,
            '2' => 2,
            '3' => 3,
            '4' => 4,
            '5' => 5,
            '6' => 6,
            '7' => 7,
            '8' => 8,
            '9' => 9,
            '10' => 10,
            '♞' => 11,
            '♛' => 12,
            '♚' => 13
        ];

        $currentPoints = 0;

        foreach ($currentHand as $card) {
            $currentPoints += $pointsTable[$card->getValue()];
        }
        $okCard = 21 - $currentPoints;

        //Get array of number of remaing cards of each value
        $number = $deck->getNumberOfCards();

        //Get the amount of cards left in the Deck
        $cardsLeft = array_sum($number);

        // Count safe cards
        $numberOfOkCards = 0;

        foreach ($number as $value => $countValues) {
            if ($pointsTable[$value] <= $okCard) {
                $numberOfOkCards += $countValues;
            }
        }

        //Avoid devide by zero
        if ($cardsLeft === 0) {
            return 0.0;
        }

        //Return the propability of not getting fat in percent
        return round($numberOfOkCards / $cardsLeft * 100, 1);
    }



}
