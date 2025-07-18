<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250614200453 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create milestone table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE milestone (
                id INT UNSIGNED AUTO_INCREMENT NOT NULL,
                title VARCHAR(127) NOT NULL,
                description LONGTEXT NOT NULL,
                start_date DATETIME NOT NULL,
                finish_date DATETIME NOT NULL,
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at DATETIME DEFAULT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            DROP TABLE milestone
        SQL);
    }
}
