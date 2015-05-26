<?php

use Phinx\Migration\AbstractMigration;

class TemplateGroupsTableSeeder extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = $this->adapter->getConnection()->prepare('INSERT INTO template_groups (`group_id`, `site_id`, `group_name`, `group_order`, `is_site_default`) VALUES (:group_id, :site_id, :group_name, :group_order, :is_site_default)');

        $query->execute([
            'group_id' => 1,
            'site_id' => 1,
            'group_name' => 'site',
            'group_order' => 1,
            'is_site_default' => 'y',
        ]);


    }

}
