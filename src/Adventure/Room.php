<?php

namespace App\Adventure;

/**
 * Class Room in the game Laundry Master
 */
class Room
{
    private string $name;
    private string $roomInfo;
    private string $image;

    /**
     * @var array<string, Room>
     */
    private array $doorTo = [];

    /**
     * @var Thing[]
     */
    private array $things = [];

    /**
     * @var Closet[]
     */
    private array $closets = [];

    /**
     * Constructor of Room
     *
     * @param Thing[] $things
     * @param Closet[] $closets
     */
    public function __construct(string $name, array $things = [], array $closets = [], string $roomInfo = "", string $image = "")
    {
        $this->name = $name;
        $this->things = $things;
        $this->closets = $closets;
        $this->roomInfo = $roomInfo;
        $this->image = $image;
    }

    /**
     * Add Thing to a room
     */
    public function addThing(Thing $thing): void
    {
        $this->things[] = $thing;
    }

    /**
     * Remove a Thing from a room
     *
     * @return bool
     */
    public function removeThing(Thing $thing): bool
    {
        $position = array_search($thing, $this->things, true);
        if ($position !== false) {
            unset($this->things[$position]);
            $this->things = array_values($this->things);
            return true;
        }
        return false;
    }

    /**
     * Get name of room
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
    * Get info of room
    *
    * @return string
    */
    public function getInfo(): string
    {
        return $this->roomInfo;
    }

    /**
    * Get image of room
    *
    * @return string
    */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * Get the Things in the room
     *
     * @return Thing[]
     */
    public function getThings(): array
    {
        return $this->things;
    }

    /**
     * Get Things in the room by id
     *
     * @return ?Thing
     */
    public function getThingById(int $id): ?Thing
    {
        foreach ($this->things as $thing) {
            if ($thing->getId() === $id) {
                return $thing;
            }
        }
        return null;
    }

    /**
     * Get closets in the room
     *
     * @return Closet[]
     */
    public function getClosets(): array
    {
        return $this->closets;
    }

    /**
     * Get closet in the room by id
     *
     * @return ?Closet
     */
    public function getClosetById(int $id): ?Closet
    {
        foreach ($this->closets as $closet) {
            if ($closet->getId() === $id) {
                return $closet;
            }
        }
        return null;
    }

    /**
     * Set where a door leads
     */
    public function setDoorTo(string $where, Room $room): void
    {
        $this->doorTo[$where] = $room;
    }

    /**
     * Get where a door leads
     */
    public function getDoorTo(string $where): ?Room
    {
        return $this->doorTo[$where] ?? null;
    }

    /**
     * Get all available doors in the room
     *
     * @return array<string, Room>
     */
    public function getAvailableDoors(): array
    {
        return $this->doorTo;
    }
}
