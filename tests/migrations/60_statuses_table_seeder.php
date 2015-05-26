<?php

use Phinx\Migration\AbstractMigration;

class StatusesTableSeeder extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = $this->adapter->getConnection()->prepare('INSERT INTO statuses (`status_id`, `site_id`, `group_id`, `status`, `status_order`, `highlight`) VALUES (:status_id, :site_id, :group_id, :status, :status_order, :highlight)');

        $query->execute([
            'status_id' => 1,
            'site_id' => 1,
            'group_id' => 1,
            'status' => 'open',
            'status_order' => 1,
            'highlight' => '009933',
        ]);

        $query->execute([
            'status_id' => 2,
            'site_id' => 1,
            'group_id' => 1,
            'status' => 'closed',
            'status_order' => 2,
            'highlight' => '990000',
        ]);


    }

}
