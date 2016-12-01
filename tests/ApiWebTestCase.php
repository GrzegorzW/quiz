<?php

namespace Tests;

use Faker\Factory;
use Faker\Generator;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;


abstract class ApiWebTestCase extends WebTestCase
{
    /** @var Client */
    protected $client;
    protected $fixtures;

    public static function container()
    {
        static::bootKernel();

        return static::$kernel->getContainer();
    }

    /** @return Generator */
    public static function getFaker()
    {
        $fakerFactory = new Factory();

        return $fakerFactory::create('pl_PL');
    }

    public function getReference($key)
    {
        if (isset($this->fixtures[$key])) {
            return $this->fixtures[$key];
        }

        return null;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setUp()
    {
        parent::setUp();
        if (!defined('SEED_DIR')) {
            define('SEED_DIR', realpath(__DIR__));
        }

        $this->fixtures = $this->loadFixtureFiles([
            SEED_DIR . '/Fixtures.yml'
        ]);

        $this->client = static::createClient();
        $this->client->disableReboot();
    }

    protected function authenticateClient($username = 'admin@admin.admin', $password = 'secret_password')
    {
        $this->client->request(
            'POST',
            '/api/token/get',
            [
                'username' => $username,
                'password' => $password
            ]
        );
        $this->assertStatusCode(200, $this->client);

        $data = $this->getResponseContent();
        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));
    }

    public function getResponseContent()
    {
        return json_decode($this->client->getResponse()->getContent(), true);
    }
}
