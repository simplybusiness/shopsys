<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180531231229 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('ALTER TABLE payment_domains DROP CONSTRAINT "payment_domains_pkey"');
        $this->sql('ALTER TABLE payment_domains ADD id SERIAL NOT NULL');
        $this->sql('ALTER TABLE payment_domains ADD PRIMARY KEY (id)');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
