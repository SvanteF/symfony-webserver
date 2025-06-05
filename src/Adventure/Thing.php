<?php

namespace App\Adventure;

/**
 * Class Thing in the game Laundry Master
 */
class Thing
{
    private string $type;
    private static int $idCounter = 1;
    private int $id;
    private bool $visible = true;

    /**
     * Constructor of Thing
     */
    public function __construct(string $type)
    {
        $this->type = $type;
        $this->id = self::$idCounter;
        self::$idCounter++;
    }

    /**
     * Get type of Thing
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set visibility of Thing
     */
    public function setVisibility(bool $visible): void
    {
        $this->visible = $visible;
    }

    /**
     * Get visibility of Thing
     *
     * @return bool
     */
    public function isVisible(): bool
    {
        return $this->visible;
    }

    /**
     * Get id of Thing
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

}
