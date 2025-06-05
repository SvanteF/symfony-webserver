<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;

/**
 * Test class for class Player
 */
class PlayerTest extends TestCase
{
    /**
     * Construct object, verify that is an instance of class Player as well as to check that it returns
     * the correct string through getName()
     */
    public function testPlayerCreateAndGetName(): void
    {
        $name = 'testName';
        $player = new Player($name);

        $this->assertInstanceOf("App\Card\Player", $player);

        $this->assertEquals($name, $player->getName());
    }

    /**
     * Construct Player object as well as a Card object (required for testing). Verify that
     * the retrieved card is the same as was initially created
     */
    public function testPlayerGiveCardAndGetHand(): void
    {
        $name = 'testName';
        $player = new Player($name);

        // Create a card object
        $value = '2';
        $color = '♥';
        $card = new Card($value, $color);

        $player->giveCard($card);
        $cardHand = $player->getCardHand();

        // Check that getCardHand returns the same Card object
        $this->assertSame($card, $cardHand[0]);
    }
}
