<?php

namespace App\Adventure;

use PHPUnit\Framework\TestCase;

/**
 * Test class for class Games
 */
class GameTest extends TestCase
{
    /**
     * Construct object and verify that an instance is created
     */
    public function testCreateGame(): void
    {
        $playerName = 'John Doe';

        $game = new Game($playerName);

        $this->assertInstanceOf("\App\Adventure\Game", $game);

        //Verify that a player was created
        $player = $game->getPlayer();
        $this->assertInstanceOf("\App\Adventure\Player", $player);

        // Verify that all 5 rooms were created
        $this->assertCount(5, $game->getRooms());
    }
}
