<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;

/**
 * Test class for class Card
 */
class CardTest extends TestCase
{
    /**
     * Construct object and verify that the object has the expected properties, including both arguments
     */
    public function testCreateObjectWithArguments(): void
    {
        $value = '2';
        $color = '♥';

        $card = new Card($value, $color);

        $this->assertInstanceOf("\App\Card\Card", $card);

        $this->assertEquals($value, $card->getValue());
        $this->assertEquals($color, $card->getColor());
    }

    /**
     * Construct object and verify that getAsString returns a correct result
     */
    public function testGetAsString(): void
    {
        $value = '2';
        $color = '♥';

        $card = new Card($value, $color);

        $exp = '[♥ 2]';

        $this->assertEquals($exp, $card->getAsString());
    }
}
