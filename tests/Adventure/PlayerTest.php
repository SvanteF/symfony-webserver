<?php

namespace App\Adventure;

use PHPUnit\Framework\TestCase;

/**
 * Test class for class Player
 */
class PlayerTest extends TestCase
{
    /**
     * Construct object and verify that an instance is created
     */
    public function testCreatePlayer(): void
    {
        $name = 'Hallen';
        $things = [];
        $closets = [];
        $playerName = 'John Doe';

        $startRoom = new Room($name, $things, $closets);
        $player = new Player($startRoom, $playerName);

        $this->assertInstanceOf("\App\Adventure\Player", $player);

        $this->assertSame('John Doe', $player->getName());
    }

    /**
     * Verify handling of basket is correct
     */
    public function testBasketPlayer(): void
    {
        $name = 'Hallen';
        $things = [];
        $closets = [];
        $playerName = 'John Doe';

        $startRoom = new Room($name, $things, $closets);
        $player = new Player($startRoom, $playerName);
        $laundry = new Laundry();

        // Verify basket is empty when player is created
        $this->assertSame([], $player->getBasket());

        $player->addLaundryToBasket($laundry);

        //Verify that the instance of Laundry is now the player´s basket (in the array)
        $this->assertContains($laundry, $player->getBasket());

        // Verify that there is exactly 1 laundry in the basket
        $this->assertSame(1, $player->getLaundryCount());

        $player->emptyBasket();

        //Verify that the basket now is empty again after it has been emptied
        $this->assertNotContains($laundry, $player->getBasket());

        // Verify that there is exactly 0 laundry in the basket
        $this->assertSame(0, $player->getLaundryCount());
    }

    /**
     * Verify handling of pocket is correct
     */
    public function testPocketPlayer(): void
    {
        $name = 'Hallen';
        $things = [];
        $closets = [];
        $playerName = 'John Doe';

        $startRoom = new Room($name, $things, $closets);
        $player = new Player($startRoom, $playerName);
        $key = new Key();

        $player->addKeyToPocket($key);

        //Verify that the key is in the player´s pocket
        $this->assertContains($key, $player->getPocket());
    }

    /**
     * Verify that the wrong key cannot open a closet but the right one can
     */
    public function testUnlockClosetByPlayer(): void
    {
        $name = 'Hallen';
        $playerName = 'John Doe';
        $closetId = 1;

        $things = [];
        $closets = [];

        $key1 = new Key(); // id = 1
        $key2 = new Key(); // id = 2

        $closets[0] = new Closet($closetId, $key1->getId());

        $startRoom = new Room($name, $things, $closets);
        $player = new Player($startRoom, $playerName);


        // Get the closet added above in room 'Hallen' in position 0.
        $closet = $startRoom->getClosets()[0];

        // Verify that false is returned if there is no key in the pocket
        $this->assertFalse($player->useKeyOnCloset($key1->getId(), $closet));

        // Give the wrong key to the player
        $player->addKeyToPocket($key2);

        // Verify that the player did not unlock the closet with the key
        $this->assertFalse($player->useKeyOnCloset($key2->getId(), $closet));

        // Throw away the wrong key
        $player->emptyPocket();

        // Give the right key to the player
        $player->addKeyToPocket($key1);

        // Verify that the player unlocked the closet with the key
        $this->assertTrue($player->useKeyOnCloset($key1->getId(), $closet));
    }

    /**
     * Verify that move works. Only allow a move in a valid direction and verify that the player ends up in the right room
     */
    public function testMovePlayer(): void
    {
        $name = 'Hallen';
        $things = [];
        $closets = [];
        $playerName = 'John Doe';

        $hallen = new Room($name, $things, $closets);
        $player = new Player($hallen, $playerName);

        $grovkok = new Room('grovkök', $things, $closets);

        $hallen->setDoorTo('väst', $grovkok);
        $grovkok->setDoorTo('öst', $hallen);

        // Verify that player cannot move in a non valid direction from current room (in this case hallen)
        $this->assertFalse($player->move('öst'));

        // Verify that player can move in a valid direction (in this case väst to grovkök)
        $this->assertTrue($player->move('väst'));

        // Verify that the player now is in grovköket
        $this->assertSame($grovkok, $player->getCurrentRoom());

        // Change current room to $hallen
        $player->setCurrentRoom($hallen);

        // Verify that the player now is in hallen
        $this->assertSame($hallen, $player->getCurrentRoom());



    }

    /**
     * Verify that Thing can correctly be collected from the room
     */
    public function testCollectThingFromRoomByPlayer(): void
    {
        $name = 'Hallen';
        $playerName = 'John Doe';
        $things = [];
        $closets = [];
        $things[0] = new Laundry();
        $things[1] = new Key();

        $hallen = new Room($name, $things, $closets);
        $player = new Player($hallen, $playerName);

        // Verify that the player collects an instance of Laundry in the basket
        $this->assertTrue($player->collectThingFromRoom($things[0]));
        $this->assertContains($things[0], $player->getBasket());

        // Verify that the player collects an instance of Key in the pocket
        $this->assertTrue($player->collectThingFromRoom($things[1]));
        $this->assertContains($things[1], $player->getPocket());

        // Verify that it is not possible to collect a thing more than once
        $this->assertFalse($player->collectThingFromRoom($things[1]));
    }

    /**
     * Verify that Thing can correctly be collected from the closet
     */
    public function testCollectThingFromClosetByPlayer(): void
    {
        $name = 'Hallen';
        $playerName = 'John Doe';
        $things = [];
        $closets = [];
        $things[0] = new Laundry();
        $things[1] = new Key();
        $closets[0] = new Closet(0);

        $closets[0]->addThing($things[0]);
        $closets[0]->addThing($things[1]);

        $hallen = new Room($name, $things, $closets);
        $player = new Player($hallen, $playerName);

        // Verify that the player collects an instance of Laundry in the basket
        $this->assertTrue($player->collectThingFromCloset($closets[0], $things[0]));
        $this->assertContains($things[0], $player->getBasket());

        // Verify that the player collects an instance of Key in the pocket
        $this->assertTrue($player->collectThingFromCloset($closets[0], $things[1]));
        $this->assertContains($things[1], $player->getPocket());

        // Verify that it is not possible to collect a thing more than once
        $this->assertFalse($player->collectThingFromCloset($closets[0], $things[1]));
    }
}
