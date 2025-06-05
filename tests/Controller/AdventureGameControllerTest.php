<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Adventure\Game;
use App\Entity\PlayerEntity;
use App\Entity\Highscore;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;

class AdventureGameControllerTest extends WebTestCase
{
    /**
     * Test GET route /proj
     */
    public function testAdventureLoadStartPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/proj');

        $this->assertResponseIsSuccessful();

        // Check that a form exists
        $this->assertSelectorExists('form');

    }

    /**
     * Test POST route /proj/game/new without name
     */
    public function testAdventureNewGameEmptyName(): void
    {
        $client = static::createClient();
        $client->request('POST', '/proj/game/new', [
            'name' => ''
        ]);

        // Verify that an empty name redirects to /proj
        $this->assertResponseRedirects('/proj');

        // Verify the right output of the flash message.
        $client->followRedirect();
        $this->assertSelectorTextContains('.flash-warning', 'Du glömde ditt namn');
    }

    /**
     * Test POST route /proj/game/new with name
     */
    public function testAdventureNewGameWithName(): void
    {
        $client = static::createClient();
        $client->request('POST', '/proj/game/new', [
            'name' => 'Test Name'
        ]);

        // Verify that play.html.twig is loaded
        $this->assertResponseIsSuccessful();

        // Verify that the name is present
        $this->assertSelectorTextContains('body', 'Test Name');
    }

    /**
     * Test GET route /proj/game with an active session
     */
    public function testAdventureGamePlayWithActiveSession(): void
    {
        $client = static::createClient();

        // Create a game to get an active session
        $client->request('POST', '/proj/game/new', [
            'name' => 'Test Name'
        ]);

        $client->request('GET', '/proj/game');

        // Verify that play.html.twig is loaded
        $this->assertResponseIsSuccessful();

        // Verify that the name is present
        $this->assertSelectorTextContains('body', 'Test Name');
    }

    /**
     * Test GET route /proj/game without an active session
     */
    public function testAdventureGamePlayWithoutActiveSession(): void
    {
        $client = static::createClient();

        $client->request('GET', '/proj/game');

        // Verify that an emty name redirects to /proj
        $this->assertResponseRedirects('/proj');
    }

    /**
     * Test GET route /proj/game/move/{where}
     */
    public function testAdventureGameMove(): void
    {
        $client = static::createClient();

        // Create a game to get an active session
        $client->request('POST', '/proj/game/new', [
            'name' => 'Test Name'
        ]);

        // Move to Viggo room (norr)
        $client->request('GET', '/proj/game/move/norr');

        // Verify that the player is not is Viggo's room
        $this->assertSelectorTextContains('h3.glow-yellow', 'Viggos rum');

        // Verify that Hallen now is an option
        $this->assertSelectorTextContains('body', 'Hallen (syd)');


        // Verify that play.html.twig is loaded
        $this->assertResponseIsSuccessful();
    }

    /**
     * Test POST route /proj/game/collect/{thingId}/{closetId}. ClosetId = 0
     */
    public function testAdventureGameCollectFromRoom(): void
    {
        $client = static::createClient();

        // Create a game to get an active session
        $crawler = $client->request('POST', '/proj/game/new', [
            'name' => 'Test Name'
        ]);

        // Find the first laundry form
        $form = $crawler->filter('form[action^="/proj/game/collect/"]')->first()->form();

        // Verify that the basket is empty
        $this->assertSelectorTextContains('body', 'Tvättkorgen är tom');

        // Collect the laundry by sending in the form
        $client->submit($form);

        // Follow the redirect
        $client->followRedirect();

        // Verify that the basket now has 1 laundry
        $this->assertSelectorTextContains('body', 'Antal plagg: 1');
    }

    /**
     * Test GET Game over
     */
    public function testAdventureGameOver(): void
    {
        $client = static::createClient();

        // Create a game to get an active session
        $client->request('POST', '/proj/game/new', [
            'name' => 'Test Name'
        ]);

        // Set start time and player in session
        $session = $client->getRequest()->getSession();
        $session->set('start_time', time() - 666);
        $session->save();

        $client->request('GET', '/proj/game/over');

        // Verify that the time is visible
        $this->assertSelectorTextContains('body', '666');

        // Verify that the session is cleared
        $session = $client->getRequest()->getSession();
        $this->assertFalse($session->has('Game'));
        $this->assertFalse($session->has('start_time'));
        $this->assertFalse($session->has('player_id'));
    }

    /**
     * Test Get About
     */
    public function testAdventureAbout(): void
    {
        $client = static::createClient();

        $client->request('GET', '/proj/about');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Information om databasen');
    }

    /**
     * Test GET about database
     */
    public function testAdventureAboutDatabase(): void
    {
        $client = static::createClient();

        $client->request('GET', '/proj/about/database');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Återställ databasen');
    }
}
