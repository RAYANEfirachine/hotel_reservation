<?php

namespace App\Tests;

use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class TestBase extends WebTestCase
{
    protected $client;

    protected function setUp(): void
    {
        // ensure previous kernel is shutdown
        self::ensureKernelShutdown();

        // create client (boots kernel)
        $this->client = static::createClient();

        $em = $this->client->getContainer()->get('doctrine')->getManager();

        $metadatas = $em->getMetadataFactory()->getAllMetadata();
        if (!empty($metadatas)) {
            $schemaTool = new SchemaTool($em);
            $schemaTool->dropSchema($metadatas);
            $schemaTool->createSchema($metadatas);
        }

        // load fixtures
        $passwordHasher = $this->client->getContainer()->has('security.password_hasher')
            ? $this->client->getContainer()->get('security.password_hasher')
            : $this->client->getContainer()->get('security.user_password_hasher');

        $fixture = new \App\DataFixtures\AppFixtures($passwordHasher);
        $fixture->load($em);
    }
}
