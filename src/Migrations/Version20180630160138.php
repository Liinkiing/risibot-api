<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180630160138 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE risipic DROP FOREIGN KEY FK_549EA2412469DE2');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP INDEX IDX_549EA2412469DE2 ON risipic');
        $this->addSql('ALTER TABLE risipic DROP category_id, DROP extension');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_549EA24F47645AE ON risipic (url)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP INDEX UNIQ_549EA24F47645AE ON risipic');
        $this->addSql('ALTER TABLE risipic ADD category_id INT NOT NULL, ADD extension VARCHAR(6) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE risipic ADD CONSTRAINT FK_549EA2412469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_549EA2412469DE2 ON risipic (category_id)');
    }
}
