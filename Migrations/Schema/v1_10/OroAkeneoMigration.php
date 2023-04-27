<?php

namespace Creativestyle\Bundle\AkeneoBundle\Migrations\Schema\v1_10;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class OroAkeneoMigration implements Migration
{
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->updateOroIntegrationTransportTable($schema);
    }

    protected function updateOroIntegrationTransportTable(Schema $schema)
    {
        $table = $schema->getTable('oro_integration_transport');
        $table->addColumn('akeneo_variant_levels', 'string', ['notnull' => false, 'length' => 255]);
    }
}
