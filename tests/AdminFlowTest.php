<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminFlowTest extends TestBase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testAdminCanLoginAndCreateRoomTypeAndRoom(): void
    {
        $client = $this->client;

        // authenticate test user directly
        $em = $this->client->getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository(\App\Entity\User::class)->findOneBy(['email' => 'admin@example.com']);
        $this->client->loginUser($user);
        $this->assertNotNull($user);

        // create room type
        $crawler = $client->request('GET', '/admin/room-types/new');
        $this->assertResponseIsSuccessful();
        $formNode = $crawler->filter('form');
        $form = $formNode->form();

        // find field names dynamically
        $typeName = $formNode->filter('input[name$="[type]"]')->attr('name');
        $capacityName = $formNode->filter('input[name$="[capacity]"]')->attr('name');
        $priceName = $formNode->filter('input[name$="[pricePerDay]"]')->attr('name');

        $form[$typeName] = 'Deluxe';
        $form[$capacityName] = 3;
        $form[$priceName] = '199.00';

        $client->submit($form);
        $client->followRedirect();
        $this->assertStringContainsString('Deluxe', $client->getResponse()->getContent());

        // create room
        $crawler = $client->request('GET', '/admin/rooms/new');
        $this->assertResponseIsSuccessful();
        $formNode = $crawler->filter('form');
        $form = $formNode->form();

        $roomNumberName = $formNode->filter('input[name$="[roomNumber]"]')->attr('name');
        $roomTypeSelect = $formNode->filter('select[name$="[roomType]"]');
        $roomTypeName = $roomTypeSelect->attr('name');
        $optionValue = $roomTypeSelect->filter('option')->first()->attr('value');

        $form[$roomNumberName] = '202';
        $form[$roomTypeName] = $optionValue;

        $client->submit($form);
        $client->followRedirect();
        $this->assertStringContainsString('202', $client->getResponse()->getContent());

        // reservations page (placeholder)
        $client->request('GET', '/admin/reservation');
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Reservations', $client->getResponse()->getContent());
    }
}
