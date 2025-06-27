<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250619221705 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create attachment table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE `attachment` (
                id INT UNSIGNED AUTO_INCREMENT NOT NULL,
                milestone_id INT UNSIGNED DEFAULT NULL,
                file_path VARCHAR(255) NOT NULL,
                file_mime_type VARCHAR(64) NOT NULL,
                file_size_bytes BIGINT NOT NULL,
                original_file_name VARCHAR(255) NOT NULL,
                description VARCHAR(255) DEFAULT NULL,
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetimetz_immutable)',
                updated_at DATETIME DEFAULT NULL,
                UNIQUE INDEX UNIQ_795FD9BB82A8E361 (file_path),
                INDEX IDX_795FD9BB4B3E2EDA (milestone_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `attachment`
                ADD CONSTRAINT FK_795FD9BB4B3E2EDA FOREIGN KEY (milestone_id) REFERENCES milestone (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE `attachment` DROP FOREIGN KEY FK_795FD9BB4B3E2EDA
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `attachment`
        SQL);
    }
}
