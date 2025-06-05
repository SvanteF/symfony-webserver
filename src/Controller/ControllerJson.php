<?php

namespace App\Controller;

use App\Card\DeckOfCards;
use App\Card\Player;
use App\Repository\LibraryRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ControllerJson extends AbstractController
{
    #[Route("/api/quote")]
    public function jsonQuote(): Response
    {
        $number = random_int(1, 3);
        $quote = "";
        $date = date('l jS \of F Y h:i:s A');


        switch ($number) {
            case 1:
                $quote = "Two things are infinite: the universe and human stupidity; and I am not sure about the universe.";
                break;
            case 2:
                $quote = "Imagination is more important than knowledge.";
                break;
            case 3:
                $quote = "I have no special talent. I am only passionately curious.";
                break;
        }

        $data = [
            'Quote of the day' => $quote,
            'Date and timestamp' => $date,
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/deck", methods: ["GET"])]
    public function jsonDeck(): Response
    {
        $deck = new DeckOfCards();

        $allCards = $deck->getDeck();
        $data = [];

        foreach ($allCards as $card) {
            $data[] = [
                'value' => $card->getValue(),
                'color' => $card->getColor(),
            ];
        }

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/deck/shuffle", methods: ["POST"])]
    public function jsonDeckShuffle(
        SessionInterface $session
    ): Response {
        $deck = new DeckOfCards();

        $shuffledDeck = $deck->shuffleAndGetDeck();

        $session->set("deck_of_cards", $shuffledDeck);

        $data = [];

        foreach ($shuffledDeck as $card) {
            $data[] = [
                'value' => $card->getValue(),
                'color' => $card->getColor(),
            ];
        }

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/deck/draw", methods: ["POST"])]
    public function jsonDeckDraw(
        SessionInterface $session
    ): Response {
        $deck = $session->get("deck_of_cards");

        $drawRes = $deck->drawCard();

        [$drawCard, $cardsLeft] = $drawRes;

        $session->set("deck_of_cards", $deck);

        $cardsLeft = count($deck->getDeck());

        $data = [
            'drawn_card' => [
                'value' => $drawCard->getValue(),
                'color' => $drawCard->getColor()
            ],
            'cards_left' => $cardsLeft
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/deck/draw/", methods: ["POST"])]
    public function jsonDeckDrawMany(
        Request $request,
        SessionInterface $session,
    ): Response {
        $num = $request->request->get('num');

        if ($num > 52) {
            throw new Exception("There are maximum 52 cards");
        }

        $deck = $session->get("deck_of_cards");

        $cardHand = [];

        for ($i = 1; $i <= $num; $i++) {
            $drawRes = $deck->drawCard();

            if ($drawRes === null) {
                break;
            }

            [$drawCard, $cardsLeft] = $drawRes;

            $cardHand[] = [
                'value' => $drawCard->getValue(),
                'color' => $drawCard->getColor(),
            ];
        }

        $cardsLeft = count($deck->getDeck());
        $session->set("deck_of_cards", $deck);

        $data = [
            'drawn_cards' => $cardHand,
            'cards_left' => $cardsLeft
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;

    }

    #[Route("/api/deck/deal/", methods: ["POST"])]
    public function jsonDeal(
        Request $request,
        SessionInterface $session
    ): Response {
        $numPlayers = $request->request->get('numPlayers');
        $numCards = $request->request->get('numCards');


        if ($numCards > 52) {
            throw new Exception("There are maximum 52 cards");
        }

        $deck = $session->get("deck_of_cards");

        $players = [];
        for ($j = 1; $j <= $numPlayers; $j++) {
            $playerName = 'Spelare ' . $j;
            $player = new Player($playerName);

            for ($i = 1; $i <= $numCards; $i++) {
                $drawRes = $deck->drawCard();

                [$drawCard, $cardsLeft] = $drawRes;

                $player->giveCard($drawCard);

            }
            $cards = [];
            foreach ($player->getCardHand() as $card) {
                $cards[] = [
                    'value' => $card->getValue(),
                    'color' => $card->getColor()
                ];
            }

            $players[] = [
                'name' => $player->getName(),
                'cards' => $cards
            ];
        }

        $cardsLeft = count($deck->getDeck());
        $session->set("deck_of_cards", $deck);

        $data = [
            'players' => $players,
            'cards_left' => $cardsLeft
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/game", methods: ["GET"])]
    public function jsonScore(
        SessionInterface $session
    ): Response {
        $game21 = $session->get('game21');

        $data = [];
        $data[] = [
            'playerPoints' => $game21->getPlayerGamePoints(),
            'bankPoints' => $game21->getBankGamePoints(),
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("api/library/books", methods: ["GET"])]
    public function jsonBooks(
        LibraryRepository $libraryRepository
    ): Response {
        $library = $libraryRepository->findAll();

        $books = [];
        foreach ($library as $book) {
            $books[] = [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'isbn' => $book->getIsbn(),
                'author' => $book->getAuthor(),
                'image' => $book->getImage()
            ];
        }

        $data = [
            'library' => $books
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("api/library/books/{isbn}", methods: ["GET"])]
    public function jsonBookIsbn(
        LibraryRepository $libraryRepository,
        string $isbn
    ): Response {
        $book = $libraryRepository->findOneBy(['isbn' => $isbn]);

        $data = [
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'isbn' => $book->getIsbn(),
            'author' => $book->getAuthor(),
            'image' => $book->getImage()
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }
}
