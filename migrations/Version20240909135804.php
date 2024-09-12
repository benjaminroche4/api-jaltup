<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240909135804 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE offer (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(120) NOT NULL, place JSON NOT NULL, url VARCHAR(255) DEFAULT NULL, job JSON NOT NULL, status VARCHAR(40) NOT NULL, tag JSON DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offer_category (offer_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_7F31A9A353C674EE (offer_id), INDEX IDX_7F31A9A312469DE2 (category_id), PRIMARY KEY(offer_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE offer_category ADD CONSTRAINT FK_7F31A9A353C674EE FOREIGN KEY (offer_id) REFERENCES offer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offer_category ADD CONSTRAINT FK_7F31A9A312469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offer_category DROP FOREIGN KEY FK_7F31A9A353C674EE');
        $this->addSql('ALTER TABLE offer_category DROP FOREIGN KEY FK_7F31A9A312469DE2');
        $this->addSql('DROP TABLE offer');
        $this->addSql('DROP TABLE offer_category');
    }
}
