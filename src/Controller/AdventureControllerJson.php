<?php

namespace App\Controller;

use App\Adventure\Game;
use App\Adventure\Laundry;
use App\Adventure\Key;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdventureControllerJson extends AbstractController
{
    /**
     * Main route for proj/api
     */
    #[Route("/proj/api", name: "adventure_api")]
    public function projApi(): Response
    {
        return $this->render('/adventure/api.html.twig');
    }

    /**
     * Set player's name
     */
    #[Route("/proj/api/player", methods: ["POST"])]
    public function jsonNamePOST(
        Request $request,
        SessionInterface $session
    ): Response {
        // Get the name from the form
        $name = (string) $request->request->get('name');

        // Create a new game
        $game = new Game($name);

        $data = [
            'name' => $game->getPlayer()->getName()
        ];

        $session->set("Game", $game);

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    /**
     * Get player's name
     */

    #[Route("/proj/api/player", methods: ["GET"])]
    public function jsonNameGET(
        SessionInterface $session
    ): Response {
        $game = $session->get("Game");

        // Catch error if no game exists in the session
        if (!$game) {

            $data = [
                'error' => 'No game has been created, create it first'
            ];

            $response = new JsonResponse($data);
            $response->setEncodingOptions(
                $response->getEncodingOptions() | JSON_PRETTY_PRINT
            );
            return $response;
        }

        $data = [
            'name' => $game->getPlayer()->getName()
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    /**
     * Add laundry to basket
     */
    #[Route("/proj/api/basket/add", methods: ["POST"])]
    public function jsonBasketPOST(
        SessionInterface $session
    ): Response {
        $game = $session->get("Game");

        // Catch error if no game or player exists in the session
        if (!$game || !$game->getPlayer()) {

            $data = [
                'error' => 'No game has been created, create it first'
            ];

            $response = new JsonResponse($data);
            $response->setEncodingOptions(
                $response->getEncodingOptions() | JSON_PRETTY_PRINT
            );
            return $response;
        }

        $player = $game->getPlayer();
        $laundry = new Laundry();
        $player->addLaundryToBasket($laundry);
        $basket = $player->getBasket();

        $data = [];

        foreach ($basket as $laundry) {
            $data[] = [
                'laundry_id' => $laundry->getId()
            ];
        }

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    /**
     * Get player's basket
     */
    #[Route("/proj/api/basket", methods: ["GET"])]
    public function jsonBasketGET(
        SessionInterface $session
    ): Response {
        $game = $session->get("Game");

        // Catch error if no game or player exists in the session
        if (!$game || !$game->getPlayer()) {

            $data = [
                'error' => 'No game has been created, create it first'
            ];

            $response = new JsonResponse($data);
            $response->setEncodingOptions(
                $response->getEncodingOptions() | JSON_PRETTY_PRINT
            );
            return $response;
        }

        $player = $game->getPlayer();
        $basket = $player->getBasket();

        $data = [];

        foreach ($basket as $laundry) {
            $data[] = [
                'laundry_id' => $laundry->getId()
            ];
        }

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    /**
     * Add key to player's pocket
     */
    #[Route("/proj/api/pocket/add", methods: ["POST"])]
    public function jsonPocketPOST(
        SessionInterface $session
    ): Response {
        $game = $session->get("Game");

        // Catch error if no game or player exists in the session
        if (!$game || !$game->getPlayer()) {

            $data = [
                'error' => 'No game has been created, create it first'
            ];

            $response = new JsonResponse($data);
            $response->setEncodingOptions(
                $response->getEncodingOptions() | JSON_PRETTY_PRINT
            );
            return $response;
        }

        $player = $game->getPlayer();
        $key = new Key();
        $player->addKeyToPocket($key);
        $pocket = $player->getPocket();

        $data = [];

        foreach ($pocket as $key) {
            $data[] = [
                'key_id' => $key->getId()
            ];
        }

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    /**
     * Get player's pocket
     */
    #[Route("/proj/api/pocket", methods: ["GET"])]
    public function jsonPocketGET(
        SessionInterface $session
    ): Response {
        $game = $session->get("Game");

        // Catch error if no game or player exists in the session
        if (!$game || !$game->getPlayer()) {

            $data = [
                'error' => 'No game has been created, create it first'
            ];

            $response = new JsonResponse($data);
            $response->setEncodingOptions(
                $response->getEncodingOptions() | JSON_PRETTY_PRINT
            );
            return $response;
        }

        $player = $game->getPlayer();
        $pocket = $player->getPocket();

        $data = [];

        foreach ($pocket as $key) {
            $data[] = [
                'key_id' => $key->getId()
            ];
        }

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    /**
     * Get info about the current room
     */
    #[Route("/proj/api/room", methods: ["GET"])]
    public function jsonRoomGET(
        SessionInterface $session
    ): Response {
        $game = $session->get("Game");

        $data = [];

        // Catch error if no game or player exists in the session
        if (!$game || !$game->getPlayer()) {

            $data = [
                'error' => 'No game has been created, create it first'
            ];

            $response = new JsonResponse($data);
            $response->setEncodingOptions(
                $response->getEncodingOptions() | JSON_PRETTY_PRINT
            );
            return $response;
        }

        $player = $game->getPlayer();
        $currentRoom = $player->getCurrentRoom();

        $data[] = [
            'currentRoom' => $currentRoom->getName(),
            'roomInfo' => $currentRoom->getInfo(),
            'roomImage' => $currentRoom->getImage()
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
        return $response;
    }
}
