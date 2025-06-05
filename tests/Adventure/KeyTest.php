<?php

namespace App\Adventure;

use PHPUnit\Framework\TestCase;

/**
 * Test class for class Key
 */
class KeyTest extends TestCase
{
    /**
     * Construct object and verify that the correct instance is created as well as correct type & visibility.
     * Also verify that the correct Id is read
     */
    public function testKey(): void
    {
        $key = new Key();

        $this->assertInstanceOf("\App\Adventure\Key", $key);
        $this->assertEquals('key', $key->getType());
        $this->assertTrue($key->isVisible());

        // Change visibility and test
        $key->setVisibility(false);
        $this->assertFalse($key->isVisible());
    }
}
