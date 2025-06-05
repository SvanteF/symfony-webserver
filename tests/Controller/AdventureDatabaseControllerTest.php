<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Adventure\Game;
use App\Entity\PlayerEntity;
use App\Entity\Highscore;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class AdventureDatabaseControllerTest extends WebTestCase
{
    /**
     * Test POST route /proj/entity/delete
     */
    public function testResetDatabase(): void
    {
        $client = static::createClient();

        // @phpstan-ignore-next-line
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        // Create a new player
        $player = new PlayerEntity();
        $player->setName('Test Name');
        $entityManager->persist($player);

        // Create a new highscore and connect to player
        $highscore = new Highscore();
        $highscore->setScore(123);
        $highscore->setPlayer($player);
        $highscore->setCreated(new DateTimeImmutable());
        $entityManager->persist($highscore);

        $entityManager->flush();

        $client->request('POST', '/proj/entity/delete');

        // Verify that an empty name redirects to /proj/about/database
        $this->assertResponseRedirects('/proj/about/database');
        $entityManager->clear();

        $this->assertCount(0, $entityManager->getRepository(Highscore::class)->findAll());

        // Verify the right output of the flash message.
        $client->followRedirect();
        $this->assertSelectorTextContains('.flash-success', 'Databasen har återställts');
    }

    /**
     * Test GET route /proj/highscore
     */
    public function testAdventureHighscorePageLoads(): void
    {
        $client = static::createClient();

        $client->request('GET', '/proj/highscore');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Highscores');
    }

    /**
     * Test all setters and getters for highscore
     */
    public function testAdventureGettersAndSetters(): void
    {
        // Create a new highscore and player entity as well as creating a date
        $highscore = new Highscore();
        $player = new PlayerEntity();
        $date = new DateTimeImmutable();

        // Set value to score, player and date
        $highscore->setScore(666);
        $highscore->setPlayer($player);
        $highscore->setCreated($date);

        // Verify that highscore gets the right values
        $this->assertEquals(666, $highscore->getScore());
        $this->assertSame($player, $highscore->getPlayer());
        $this->assertSame($date, $highscore->getCreated());
    }

    public function testHighscoreGetId(): void
    {
        // @phpstan-ignore-next-line
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        // Create a new player
        $player = new PlayerEntity();
        $player->setName('Test Name');
        $entityManager->persist($player);
        $entityManager->flush();

        // Create a new highscore and connect to player
        $highscore = new Highscore();
        $highscore->setScore(123);
        $highscore->setPlayer($player);
        $highscore->setCreated(new DateTimeImmutable()); // <-- Lägg till denna rad!
        $entityManager->persist($highscore);
        $entityManager->flush();

        $this->assertNotNull($highscore->getId());
        $this->assertIsInt($highscore->getId());
    }

    /**
     * Test all setters and getters for highscore
     */
    public function testAdventureGettersAndSettersHighscore(): void
    {
        // @phpstan-ignore-next-line
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        // Create a new player entity and persist it
        $player = new PlayerEntity();
        $player->setName('Test Name');
        $entityManager->persist($player);
        $entityManager->flush();

        // Create a new highscore and set values
        $highscore = new Highscore();
        $date = new DateTimeImmutable();
        $highscore->setScore(1);
        $highscore->setPlayer($player);
        $highscore->setCreated($date);

        $entityManager->persist($highscore);
        $entityManager->flush();

        // Verify that highscore gets the right values
        $this->assertEquals(1, $highscore->getScore());
        $this->assertSame($player, $highscore->getPlayer());
        $this->assertSame($date, $highscore->getCreated());

        // Verify that highscore now has an id from the database
        $this->assertNotNull($highscore->getId());
        $this->assertIsInt($highscore->getId());
    }

    /**
     * Test remaining setters and getters for player
     */
    public function testAdventureGettersAndSettersPlayer(): void
    {
        $player = new PlayerEntity();
        $player->setName('Test Name');

        $this->assertEquals('Test Name', $player->getName());
    }

    /**
     * Test GET quick solution page
     */
    public function testAdventureQuick(): void
    {
        $client = static::createClient();

        $client->request('GET', '/proj/quick');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Så här kan du spela genom spelet snabbt och enkelt');
    }
}
