<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260105000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create RoomType, Room, Reservation, Payment and User tables (skeleton)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE room_type (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(100) NOT NULL, capacity INT NOT NULL, price_per_day NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room (id INT AUTO_INCREMENT NOT NULL, room_type_id INT NOT NULL, room_number VARCHAR(50) NOT NULL, status VARCHAR(50) NOT NULL, INDEX IDX_ROOM_TYPE (room_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(100) DEFAULT NULL, last_name VARCHAR(100) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, birth_date DATE DEFAULT NULL, identity_type VARCHAR(50) DEFAULT NULL, identity_number VARCHAR(100) DEFAULT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_USER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, room_id INT NOT NULL, check_in_date DATE NOT NULL, check_out_date DATE NOT NULL, status VARCHAR(50) NOT NULL, total_price NUMERIC(10, 2) NOT NULL, INDEX IDX_RES_USER (user_id), INDEX IDX_RES_ROOM (room_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, reservation_id INT NOT NULL, amount NUMERIC(10, 2) NOT NULL, payment_method VARCHAR(50) NOT NULL, payment_status VARCHAR(50) NOT NULL, payment_date DATETIME DEFAULT NULL, INDEX IDX_PAYMENT_RES (reservation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_ROOM_TYPE FOREIGN KEY (room_type_id) REFERENCES room_type (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_RES_USER FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_RES_ROOM FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_PAYMENT_RES FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_PAYMENT_RES');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_RES_ROOM');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_RES_USER');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_ROOM_TYPE');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE room_type');
    }
}
