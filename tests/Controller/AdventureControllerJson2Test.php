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

class AdventureControllerJson2Test extends WebTestCase
{
    /**
    * Test POST json route - Add key to pocket without an active session
    */
    public function testAdventureJsonAddKeyToPocketNoActiveSession(): void
    {
        $client = static::createClient();

        // Add key to pocket
        $client->request('POST', '/proj/api/pocket/add');

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent() ?: '', true);

        // Verify that an error is thrown if there is no game
        $this->assertEquals('No game has been created, create it first', $data['error']);
    }

    /**
     * Test POST json route - Add key to pocket with an active session
     */
    public function testAdventureJsonAddKeyToPocketActiveSession(): void
    {
        $client = static::createClient();

        // Create a game to get an active session
        $client->request('POST', '/proj/api/player', [
            'name' => 'Test Name'
        ]);
        $this->assertResponseIsSuccessful();

        // Add key to pocket
        $client->request('POST', '/proj/api/pocket/add');
        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent() ?: '', true);

        // Verify that a key has been added to the pocket
        $this->assertArrayHasKey('key_id', $data[0]);
    }

    /**
     * Test GET json route - Get key from pocket without an active session
     */
    public function testAdventureJsonGetKeyFromPocketNoActiveSession(): void
    {
        $client = static::createClient();

        $client->request('GET', '/proj/api/pocket');

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent() ?: '', true);

        // Verify that an error is thown if there is no game
        $this->assertEquals('No game has been created, create it first', $data['error']);
    }

    /**
     * Test GET json route - Get key from pocket with an active session
     */
    public function testAdventureJsonGetKeyFromPocketActiveSession(): void
    {
        $client = static::createClient();

        // Create a game to get an active session
        $client->request('POST', '/proj/api/player', [
            'name' => 'Test Name'
        ]);
        $this->assertResponseIsSuccessful();

        // Add key to pocket
        $client->request('POST', '/proj/api/pocket/add');
        $this->assertResponseIsSuccessful();

        // Get the key from pocket
        $client->request('GET', '/proj/api/pocket');
        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent() ?: '', true);

        // Verify that a key was read correctly
        $this->assertArrayHasKey('key_id', $data[0]);
    }

    /**
    * Test GET json route - Get information about the current room without an active session
    */
    public function testAdventureJsonGetRoomNoActiveSession(): void
    {
        $client = static::createClient();

        $client->request('GET', '/proj/api/room');

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent() ?: '', true);

        // Verify that an error is thrown if there is no game
        $this->assertEquals('No game has been created, create it first', $data['error']);
    }

    /**
     * Test GET json route - Get information about the current room with an active session
     */
    public function testAdventureJsonGetRoomActiveSession(): void
    {
        $client = static::createClient();

        // Create a game to get an active session
        $client->request('POST', '/proj/api/player', [
            'name' => 'Test Name'
        ]);
        $this->assertResponseIsSuccessful();

        // Get the room
        $client->request('GET', '/proj/api/room');
        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent() ?: '', true);

        // Verify that currentRoom, roomInfo and roomImage are read
        $this->assertArrayHasKey('currentRoom', $data[0]);
        $this->assertArrayHasKey('roomInfo', $data[0]);
        $this->assertArrayHasKey('roomImage', $data[0]);
    }
}
