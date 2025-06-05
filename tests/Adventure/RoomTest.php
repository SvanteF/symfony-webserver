<?php

namespace App\Adventure;

use PHPUnit\Framework\TestCase;

/**
 * Test class for class Room
 */
class RoomTest extends TestCase
{
    /**
     * Construct object and verify that an instance is created
     */
    public function testCreateRoom(): void
    {
        $name = 'Hallen';
        $things = [];
        $closets = [];

        $room = new Room($name, $things, $closets);

        $this->assertInstanceOf("\App\Adventure\Room", $room);

        $this->assertSame('Hallen', $room->getName());
    }

    /**
     * Verify that a Thing is added to the Room and that it is retrieved with getThings()
     */
    public function testAddAndGetThingRoom(): void
    {
        $name = 'Hallen';
        $things = [];
        $closets = [];

        $room = new Room($name, $things, $closets);

        $thing = new Thing('laundry');

        $room->addThing($thing);

        // Verify that getThings() return the object that was added
        $this->assertContains($thing, $room->getThings());

    }

    /**
     * Verify that a Thing is added to the Closet and that it is retrieved with getThings()
     */
    public function testRemoveThingsFromRoom(): void
    {
        $name = 'Hallen';
        $things = [];
        $closets = [];

        $room = new Room($name, $things, $closets);

        $thing = new Thing('laundry');


        // Verify that nothing can be removed if the room is empty
        $this->assertFalse($room->removeThing($thing));

        $room->addThing($thing);

        $this->assertTrue($room->removeThing($thing));
        $this->assertNotContains($thing, $room->getThings());
    }

    /**
     * Verify that a Closet can be added to a room and retrieved with getClosets()
     */
    public function testGetClosetFromRoom(): void
    {
        $name = 'Hallen';
        $things = [];
        $closet1 = new Closet(0);
        $closet2 = new Closet(1);


        $room = new Room($name, $things, [$closet1, $closet2]);

        // Get the closet
        $closets = $room->getClosets();

        //Verify getClosets()
        $this->assertCount(2, $closets);
        $this->assertSame($closet1, $closets[0]);
        $this->assertSame($closet2, $closets[1]);
    }

    /**
     * Verify doors can be set and retrieved
     */
    public function testsetAndGetDoorToRoom(): void
    {
        $hallen = new Room('Hallen');
        $grovkoket = new Room('Grovköket');

        // Add door between hallen and grovköket
        $hallen->setDoorTo('grovköket', $grovkoket);

        //Verify the Rooms are the same
        $this->assertSame($grovkoket, $hallen->getDoorTo('grovköket'));

        // Verify that 1 door has been added
        $this->assertCount(1, $hallen->getAvailableDoors());
    }

    /**
    * Verify that an id can be read
    */
    public function testIdRoom(): void
    {

        $closetId = 1;
        $closets = [];
        $closets[0] = new Closet($closetId);

        $thing = new Thing('laundry');
        $room1 = new Room('rumMedGarderob', [], $closets);
        $room2 = new Room('rumUtanGarderob');

        // Verify that there is no closet in the room
        $this->assertNull($room2->getClosetById($closetId));

        // Verify that the right closet is fetched with the right id
        $this->assertSame($closets[0], $room1->getClosetById($closetId));

        // Verify the method returns null if there is no Thing in the room
        $this->assertSame(null, $room1->getThingById($thing->getId()));

        // Add a thing to closet
        $room1->addThing($thing);

        // Verify that room can get the correct thing id.
        $this->assertSame($thing, $room1->getThingById($thing->getId()));

    }

    /**
     * Verify that the correct info and image are read
     */

    public function testInfoAndImageRoom(): void
    {
        $info = "Testar att de funkar";
        $image = "img/test.png";
        $room = new Room('testRum', [], [], $info, $image);

        //Verify that getInfo() returns the correct string
        $this->assertSame($info, $room->getInfo());

        //Verify that getImage() returns the correct string
        $this->assertSame($image, $room->getImage());
    }
}
