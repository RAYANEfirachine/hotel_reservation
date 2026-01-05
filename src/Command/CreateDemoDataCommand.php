<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\RoomType;
use App\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:create-demo-data', description: 'Create demo admin/client user and sample room data')]
class CreateDemoDataCommand extends Command
{
    public function __construct(private EntityManagerInterface $em, private UserPasswordHasherInterface $hasher)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Admin user
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setFirstName('Admin');
        $admin->setLastName('User');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'adminpass'));

        // Client user
        $client = new User();
        $client->setEmail('client@example.com');
        $client->setFirstName('Client');
        $client->setLastName('User');
        $client->setRoles(['ROLE_CLIENT']);
        $client->setPassword($this->hasher->hashPassword($client, 'clientpass'));

        // RoomType
        $rt = new RoomType();
        $rt->setType('Standard');
        $rt->setCapacity(2);
        $rt->setPricePerDay('99.99');

        // Room
        $room = new Room();
        $room->setRoomNumber('101');
        $room->setRoomType($rt);

        $this->em->persist($admin);
        $this->em->persist($client);
        $this->em->persist($rt);
        $this->em->persist($room);
        $this->em->flush();

        $io->success('Demo data created: admin@example.com / adminpass, client@example.com / clientpass');

        return Command::SUCCESS;
    }
}
