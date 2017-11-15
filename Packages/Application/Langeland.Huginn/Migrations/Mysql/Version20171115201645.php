<?php
namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs! This block will be used as the migration description if getDescription() is not used.
 */
class Version20171115201645 extends AbstractMigration
{

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Add GitHubEvent models';
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');
        $this->addSql('CREATE TABLE langeland_huginn_domain_model_githubevent (identifier VARCHAR(255) NOT NULL, received DATETIME NOT NULL, delivery VARCHAR(255) DEFAULT NULL, event VARCHAR(255) DEFAULT NULL, signature VARCHAR(255) DEFAULT NULL, payload LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', PRIMARY KEY(identifier)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');
        $this->addSql('DROP TABLE langeland_huginn_domain_model_githubevent');
    }
}