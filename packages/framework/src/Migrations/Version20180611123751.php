<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\FrameworkBundle\Component\Migration\MultidomainMigrationTrait;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180611123751 extends AbstractMigration
{
    use MultidomainMigrationTrait;

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        // In previous implementation, records in transport_domains were only created when the transport was enabled on the domain
        // Currently, there should be a record for each transport - domain pair
        $this->sql('ALTER TABLE transport_domains ADD enabled BOOLEAN NOT NULL DEFAULT TRUE');
        $this->sql('ALTER TABLE transport_domains ALTER enabled DROP DEFAULT;');
        $this->sql('CREATE UNIQUE INDEX transport_domain ON transport_domains (transport_id, domain_id)');

        // Because there is a compound unique key for transport and domain we can insert the remaining records
        foreach ($this->getAllDomainIds() as $domainId) {
            $this->sql(
                'INSERT INTO transport_domains (transport_id, domain_id, enabled) 
                    SELECT id, :domainId, FALSE FROM transports
                    ON CONFLICT DO NOTHING',
                ['domainId' => $domainId]
            );
        }

        $this->sql('ALTER TABLE transport_domains DROP CONSTRAINT FK_18AC7F6C9909C13F');
        $this->sql('
            ALTER TABLE
                transport_domains
            ADD
                CONSTRAINT FK_18AC7F6C9909C13F FOREIGN KEY (transport_id) REFERENCES transports (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
