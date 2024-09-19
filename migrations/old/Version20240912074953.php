<?php

declare(strict_types=1);

namespace DoctrineMigrations\old;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240912074953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4FBF094FB5B48B91 ON company (public_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_29D6873EB5B48B91 ON offer (public_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_4FBF094FB5B48B91 ON company');
        $this->addSql('DROP INDEX UNIQ_29D6873EB5B48B91 ON offer');
    }
}
