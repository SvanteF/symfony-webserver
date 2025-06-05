<?php

namespace App\Adventure;

/**
 * Class Laundry in the game Laundry Master. Laundry can be in a Room or Closet
 */
class Laundry extends Thing
{
    /**
    * Constructor of Laundry
    */
    public function __construct()
    {
        parent::__construct('laundry');
    }

}
