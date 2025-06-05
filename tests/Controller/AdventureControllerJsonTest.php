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

class AdventureControllerJsonTest extends WebTestCase
{
    /**
     * Test GET main route for /proj/api
     */
    public function testAdventureApi(): void
    {
        $client = static::createClient();

        $client->request('GET', '/proj/api');

        // Verify correct response and that the correct page was loaded
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Sammanställning av JSON routes för projektet');
    }

    /**
     * Test POST json route for setting a players name
     */
    public function testAdventureJsonSetPlayer(): void
    {
        $client = static::createClient();

        // Create a game to get an active session
        $client->request('POST', '/proj/api/player', [
            'name' => 'Test Name'
        ]);

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent() ?: '', true);

        // Verify that the correct name is set
        $this->assertEquals('Test Name', $data['name']);
    }

    /**
     * Test GET json route - player's name without an active session
     */
    public function testAdventureJsonGetPlayerNoActiveSession(): void
    {
        $client = static::createClient();

        // Get player's name
        $client->request('GET', '/proj/api/player');

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent() ?: '', true);

        // Verify that an error is thrown if there is no game
        $this->assertEquals('No game has been created, create it first', $data['error']);
    }

    /**
     * Test GET json route - player's name with an active session
     */
    public function testAdventureJsonGetPlayerActiveSession(): void
    {
        $client = static::createClient();

        // Create a game to get an active session
        $client->request('POST', '/proj/api/player', [
            'name' => 'Test Name'
        ]);

        $this->assertResponseIsSuccessful();

        // Get player's name
        $client->request('GET', '/proj/api/player');
        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent() ?: '', true);

        // Verify that getPlayer works correctly
        $this->assertEquals('Test Name', $data['name']);
    }
    /**
     * Test POST json route - Add laundry to basket without an active session
     */
    public function testAdventureJsonAddLaundryToBasketNoActiveSession(): void
    {
        $client = static::createClient();

        // Add laundry to basket
        $client->request('POST', '/proj/api/basket/add');

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent() ?: '', true);

        // Verify that an error is thown if there is no game
        $this->assertEquals('No game has been created, create it first', $data['error']);
    }

    /**
     * Test POST json route - Add laundry to basket with an active session
     */
    public function testAdventureJsonAddLaundryToBasketActiveSession(): void
    {
        $client = static::createClient();

        // Create a game to get an active session
        $client->request('POST', '/proj/api/player', [
            'name' => 'Test Name'
        ]);
        $this->assertResponseIsSuccessful();

        // Add laundry to basket
        $client->request('POST', '/proj/api/basket/add');
        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent() ?: '', true);

        // Verify that laundry has been added to the basket
        $this->assertArrayHasKey('laundry_id', $data[0]);
    }

    /**
     * Test GET json route - Get laundry from basket without an active session
     */
    public function testAdventureJsonGetLaundryFromBasketNoActiveSession(): void
    {
        $client = static::createClient();

        $client->request('GET', '/proj/api/basket');

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent() ?: '', true);

        // Verify that an error is thown if there is no game
        $this->assertEquals('No game has been created, create it first', $data['error']);
    }

    /**
     * Test GET json route - Get laundry from basket with an active session
     */
    public function testAdventureJsonGetLaundryFromBasketActiveSession(): void
    {
        $client = static::createClient();

        // Create a game to get an active session
        $client->request('POST', '/proj/api/player', [
            'name' => 'Test Name'
        ]);
        $this->assertResponseIsSuccessful();

        // Add laundry to basket
        $client->request('POST', '/proj/api/basket/add');
        $this->assertResponseIsSuccessful();

        // Get the laundry from basket
        $client->request('GET', '/proj/api/basket');
        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent() ?: '', true);

        // Verify that laundry was read correctly
        $this->assertArrayHasKey('laundry_id', $data[0]);
    }
}
