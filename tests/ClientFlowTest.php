<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ClientFlowTest extends TestBase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testClientCanLoginViewRoomsCreateAndCancelReservation(): void
    {
        $client = $this->client;

        // authenticate test user directly
        $em = $this->client->getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository(\App\Entity\User::class)->findOneBy(['email' => 'client@example.com']);
        $this->client->loginUser($user);
        $this->assertNotNull($user);

        // view rooms
        $client->request('GET', '/rooms/');
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Available Rooms', $client->getResponse()->getContent());

        // create reservation
        $crawler = $client->request('GET', '/booking/new');
        $this->assertResponseIsSuccessful();
        $formNode = $crawler->filter('form');
        $form = $formNode->form();

        $roomField = $formNode->filter('select[name$="[room]"]');
        $roomName = $roomField->attr('name');
        $option = $roomField->filter('option')->first()->attr('value');

        $checkInName = $formNode->filter('input[name$="[checkInDate]"]')->attr('name');
        $checkOutName = $formNode->filter('input[name$="[checkOutDate]"]')->attr('name');

        $form[$roomName] = $option;
        $form[$checkInName] = (new \DateTime('+1 day'))->format('Y-m-d');
        $form[$checkOutName] = (new \DateTime('+2 days'))->format('Y-m-d');

        $client->submit($form);
        $client->followRedirect();
        $this->assertStringContainsString('Reservation Created', $client->getResponse()->getContent());

        // find reservation id from DB
        $em = $this->client->getContainer()->get('doctrine')->getManager();
        $res = $em->getRepository(\App\Entity\Reservation::class)->findOneBy([], ['id' => 'DESC']);
        $this->assertNotNull($res);
        $id = $res->getId();

        // cancel reservation
        // controller does not validate csrf token here in tests, send a dummy token
        $client->request('POST', '/booking/' . $id . '/cancel', ['_token' => 'dummy']);
        $client->followRedirect();
        $this->assertStringContainsString('Reservation cancelled', $client->getResponse()->getContent());
    }
}
