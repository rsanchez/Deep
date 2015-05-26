<?php

use Phinx\Migration\AbstractMigration;

class StatusGroupsTableSeeder extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = $this->adapter->getConnection()->prepare('INSERT INTO status_groups (`group_id`, `site_id`, `group_name`) VALUES (:group_id, :site_id, :group_name)');

        $query->execute([
            'group_id' => 1,
            'site_id' => 1,
            'group_name' => 'Statuses',
        ]);


    }

}
