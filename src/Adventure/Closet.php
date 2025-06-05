<?php

namespace App\Adventure;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class Closet in the game Laundry Master
 */
class Closet
{
    private bool $locked;

    private int $id;

    private ?int $keyId = null;

    /**
     * @var Thing[]
     */
    private array $things = [];

    public function __construct(int $id, ?int $keyId = null)
    {
        $this->id = $id;
        $this->keyId = $keyId;
        $this->locked = $keyId !== null;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Unlock a door
     *
     * @return bool
     */
    public function unlock(Key $key): bool
    {
        if ($this->locked && $key->getId() === $this->keyId) {
            $this->locked = false;
            return true;
        }
        return false;
    }

    public function isLocked(): bool
    {
        return $this->locked;
    }

    public function getThingById(int $id): ?Thing
    {
        foreach ($this->things as $thing) {
            if ($thing->getId() === $id) {
                return $thing;
            }
        }
        return null;
    }

    public function addThing(Thing $thing): void
    {
        $this->things[] = $thing;
    }

    public function removeThing(Thing $thing): bool
    {
        //Only allow things to be removed if the door is unlocked
        if ($this->locked) {
            return false;
        }

        $position = array_search($thing, $this->things, true);
        if ($position !== false) {
            unset($this->things[$position]);
            $this->things = array_values($this->things);
            return true;
        }
        return false;
    }

    /**
     * @return Thing[]
     */
    public function getThings(): array
    {
        return $this->things;
    }
}
