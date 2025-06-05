<?php

namespace App\Adventure;

/**
 * Class Key in the game Laundry Master. A key can open closets
 */
class Key extends Thing
{
    /**
     * Constructor of Key
     */
    public function __construct()
    {
        parent::__construct('key');
    }

}
