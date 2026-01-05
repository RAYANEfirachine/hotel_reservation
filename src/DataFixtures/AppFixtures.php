<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\RoomType;
use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setFirstName('Admin');
        $admin->setLastName('User');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'adminpass'));

        $client = new User();
        $client->setEmail('client@example.com');
        $client->setFirstName('Client');
        $client->setLastName('User');
        $client->setRoles(['ROLE_CLIENT']);
        $client->setPassword($this->hasher->hashPassword($client, 'clientpass'));

        $rt = new RoomType();
        $rt->setType('Standard');
        $rt->setCapacity(2);
        $rt->setPricePerDay('99.99');

        $room = new Room();
        $room->setRoomNumber('101');
        $room->setRoomType($rt);

        $manager->persist($admin);
        $manager->persist($client);
        $manager->persist($rt);
        $manager->persist($room);
        $manager->flush();
    }
}
