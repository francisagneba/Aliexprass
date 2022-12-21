<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221220164543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, image VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description CLOB NOT NULL, more_informations CLOB DEFAULT NULL, price DOUBLE PRECISION NOT NULL, is_best_seller BOOLEAN DEFAULT NULL, is_new_arrival BOOLEAN DEFAULT NULL, is_featured BOOLEAN DEFAULT NULL, is_special_offer BOOLEAN DEFAULT NULL, image VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE product_categories (product_id INTEGER NOT NULL, categories_id INTEGER NOT NULL, PRIMARY KEY(product_id, categories_id), CONSTRAINT FK_A99419434584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A9941943A21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_A99419434584665A ON product_categories (product_id)');
        $this->addSql('CREATE INDEX IDX_A9941943A21214B7 ON product_categories (categories_id)');
        $this->addSql('CREATE TABLE related_product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_id INTEGER NOT NULL, CONSTRAINT FK_EC53CE084584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_EC53CE084584665A ON related_product (product_id)');
        $this->addSql('CREATE TABLE reviews_product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, product_id INTEGER NOT NULL, note INTEGER NOT NULL, comment CLOB DEFAULT NULL, CONSTRAINT FK_E0851D6CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_E0851D6C4584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_E0851D6CA76ED395 ON reviews_product (user_id)');
        $this->addSql('CREATE INDEX IDX_E0851D6C4584665A ON reviews_product (product_id)');
        $this->addSql('CREATE TABLE tags_product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE tags_product_product (tags_product_id INTEGER NOT NULL, product_id INTEGER NOT NULL, PRIMARY KEY(tags_product_id, product_id), CONSTRAINT FK_90ACD5EF273D7ABB FOREIGN KEY (tags_product_id) REFERENCES tags_product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_90ACD5EF4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_90ACD5EF273D7ABB ON tags_product_product (tags_product_id)');
        $this->addSql('CREATE INDEX IDX_90ACD5EF4584665A ON tags_product_product (product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_categories');
        $this->addSql('DROP TABLE related_product');
        $this->addSql('DROP TABLE reviews_product');
        $this->addSql('DROP TABLE tags_product');
        $this->addSql('DROP TABLE tags_product_product');
    }
}
