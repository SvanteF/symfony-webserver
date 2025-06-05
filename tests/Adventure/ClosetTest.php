<?php

namespace App\Adventure;

use PHPUnit\Framework\TestCase;

/**
 * Test class for class Closet
 */
class ClosetTest extends TestCase
{
    /**
     * Construct object and verify that an instance is created
     */
    public function testCreateClosetWithParameter(): void
    {
        $keyId = 0;
        $closet = new Closet($keyId);

        $this->assertInstanceOf("\App\Adventure\Closet", $closet);

    }

    /**
     * Construct object and verify that an instance is created
     */
    public function testCreateClosetWithoutParameter(): void
    {
        $closetId = 0;
        $closet = new Closet($closetId);

        $this->assertInstanceOf("\App\Adventure\Closet", $closet);

    }

    /**
     * Verify that lock status can be read, that it can be unlocked with the right key and that the status changes to unlocked
     */
    public function testUnlockClosetWithRightKey(): void
    {
        $keyId = 1;
        $closetId = 1;
        $key = new Key();
        $closet = new Closet($closetId, $keyId);

        // Verify lock status is locked if a key is used
        $this->assertSame(true, $closet->isLocked());

        // Verify closet is unlocked with right key
        $this->assertTrue($closet->unlock($key));

        // Verify lock status is now unlocked if a key was used
        $this->assertSame(false, $closet->isLocked());

    }

    /**
     * Verify that lock status can be read, that it won´t be unlocked with the wrong key and that the status does not change to unlocked afterwards
     */
    public function testUnlockClosetWithWrongKey(): void
    {
        $closetId = 1;
        $anotherKeyId = 1;
        $key = new Key();
        $closet = new Closet($closetId, $anotherKeyId);

        // Verify lock status is locked if a key is used
        $this->assertSame(true, $closet->isLocked());

        // Verify closet is not unlocked with wrong key
        $this->assertFalse($closet->unlock($key));

        // Verify lock status is still locked
        $this->assertSame(true, $closet->isLocked());

    }

    /**
     * Verify that a Thing is added to the Closet and that it is retrieved with getThings()
     */
    public function testAddAndGetThingCloset(): void
    {
        $keyId = 0;
        $closet = new Closet($keyId);

        $thing = new Thing('key');

        $closet->addThing($thing);

        // Verify that that getThings() return the object that was added
        $this->assertContains($thing, $closet->getThings());

    }

    /**
    * Verify that a Thing is removed from Closet if it is open.
    */
    public function testRemoveThingsFromCloset(): void
    {
        // Add Thing to the closet
        $closetId1 = 1;
        $closetId2 = 2;
        $keyId = 999;
        $key = new Key();
        $closet1 = new Closet($closetId1, $keyId);
        $closet2 = new Closet($closetId2);
        $thing = new Thing('key');

        //Verify that Things can not be removed when the door is locked
        $this->assertFalse($closet1->removeThing($thing));

        //Unlock closet so Things can be removed
        $closet1->unlock($key);

        // Verify that nothing can be removed if the closet is empty
        $this->assertFalse($closet2->removeThing($thing));

        // Add a thing
        $closet2->addThing($thing);

        $this->assertTrue($closet2->removeThing($thing));
    }

    /**
    * Verify that a id can be read
    */
    public function testIdCloset(): void
    {
        $closetId = 1;
        $keyId = 1;
        $thing = new Thing('laundry');
        $closet = new Closet($closetId, $keyId);

        // Verify the method returns null if there is no Thing in the closet
        $this->assertSame(null, $closet->getThingById($thing->getId()));

        // Add a thing to closet
        $closet->addThing($thing);

        //Verify that the correct id is read
        $this->assertSame($closetId, $closet->getId());

        // Verify that closet can get the correct thing id.
        $this->assertSame($thing, $closet->getThingById($thing->getId()));

    }
}
