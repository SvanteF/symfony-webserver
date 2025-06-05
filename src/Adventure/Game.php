<?php

namespace App\Adventure;

/**
 * Class Game in the game Laundry Master
 */
class Game
{
    /**
     * @var Room[]
     */
    private array $rooms = [];
    private Player $player;

    /**
     * Info about the rooms
     */
    private string $infoHallen = "Du befinner dig i hallen. Härifrån kan du nå alla sovrum. Finns det smutskläder här? Plocka upp dem i så fall.";
    private string $infoViggosRum = "Välkommen till Viggos rum. Här var det ju ganska välstädat ändå. Men vad gömmer sig i garderoberna?";
    private string $infoAmeliesRum = "Aj aj. Du har gått in i tonåringens rum. Lycka till.";
    private string $infoFabiansRum = "Hos Fabian är det lite stökigt och massor av lego på golvet. Akta fötterna och se till att få med all tvätt. Och du, vad är det som ligger där på golvet bredvid smutstvätten?";
    private string $infoGrovkok = "Välkommen till tvättmaskinens hem.";

    /**
     * Room images
     */
    private string $imageHallen = "img/hallen.png";
    private string $imageViggosRum = "img/viggo.png";
    private string $imageAmeliesRum = "img/amelie.png";
    private string $imageFabiansRum = "img/fabian.png";
    private string $imageGrovkok = "img/grovkok.png";

    /**
     * Construcor of Game
     */
    public function __construct(string $playerName)
    {
        /**
         * Create 5 keys
         */
        $keys = [];
        for ($i = 0; $i < 5; $i++) {
            $keys[] = new Key();
        }

        /**
         * Create 15 pieces of laundry
         */
        $laundries = [];
        for ($i = 0; $i < 15; $i++) {
            $laundries[] = new Laundry();
        }

        /**
         * Create 5 closets
         */
        $closets = [];

        $closets[] = new Closet(1, 1); // $closets[0]
        $closets[] = new Closet(2, 2); // $closets[1]
        $closets[] = new Closet(3, 3); // $closets[2]
        $closets[] = new Closet(4, 4); // $closets[3]
        $closets[] = new Closet(5, 5); // $closets[4]


        /**
         * Add laundry to closets
         */
        $closets[0]->addThing($laundries[0]);
        $closets[1]->addThing($laundries[1]);
        $closets[1]->addThing($laundries[2]);
        $closets[4]->addThing($laundries[3]);
        $closets[4]->addThing($laundries[4]);


        /**
         * Add key to closet
         */
        //
        $closets[1]->addThing($keys[4]);
        $closets[2]->addThing($keys[3]);
        $closets[3]->addThing($keys[0]);
        $closets[4]->addThing($keys[2]);


        /**
         * Create all 5 rooms
         */
        $hallen = new Room('Hallen', [$laundries[5], $laundries[6], $laundries[7]], [], $this->infoHallen, $this->imageHallen);
        $viggosRoom = new Room('Viggos rum', [$laundries[8]], [$closets[0], $closets[1]], $this->infoViggosRum, $this->imageViggosRum);
        $ameliesRoom = new Room('Amélies rum', [$laundries[9], $laundries[10]], [$closets[2]], $this->infoAmeliesRum, $this->imageAmeliesRum);
        $fabiansRoom = new Room('Fabians rum', [$laundries[11], $laundries[12], $laundries[13], $keys[1]], [$closets[3], $closets[4]], $this->infoFabiansRum, $this->imageFabiansRum);
        $grovkok = new Room('Grovkök', [$laundries[14]], [], $this->infoGrovkok, $this->imageGrovkok);

        /**
         * Connect hallen to all other rooms
         */
        $hallen->setDoorTo('norr', $viggosRoom);
        $hallen->setDoorTo('öst', $ameliesRoom);
        $hallen->setDoorTo('syd', $fabiansRoom);
        $hallen->setDoorTo('väst', $grovkok);

        /**
         * Connect all other rooms to hallen
         */
        $viggosRoom->setDoorTo('syd', $hallen);
        $ameliesRoom->setDoorTo('väst', $hallen);
        $fabiansRoom->setDoorTo('norr', $hallen);
        $grovkok->setDoorTo('öst', $hallen);


        $this->rooms = [$hallen, $viggosRoom, $ameliesRoom, $fabiansRoom, $grovkok];

        /**
         * Add player and start position
         */
        $this->player = new Player($hallen, $playerName);
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * @return Room[]
     */
    public function getRooms(): array
    {
        return $this->rooms;
    }
}
