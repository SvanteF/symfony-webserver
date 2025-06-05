<?php

namespace App\Adventure;

use PHPUnit\Framework\TestCase;

/**
 * Test class for class Laundry
 */
class LaundryTest extends TestCase
{
    /**
     * Construct object and verify that the correct instance is created as well as correct type & visibility
     */
    public function testLaundry(): void
    {
        $laundry = new Laundry();

        $this->assertInstanceOf("\App\Adventure\Laundry", $laundry);
        $this->assertEquals('laundry', $laundry->getType());
        $this->assertTrue($laundry->isVisible());

        // Change visibility and test
        $laundry->setVisibility(false);
        $this->assertFalse($laundry->isVisible());
    }
}
