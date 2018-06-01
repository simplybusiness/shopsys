<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180603135339 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('ALTER TABLE transport_domains DROP CONSTRAINT "transport_domains_pkey"');
        $this->sql('ALTER TABLE transport_domains ADD id SERIAL NOT NULL');
        $this->sql('ALTER TABLE transport_domains ADD PRIMARY KEY (id)');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
