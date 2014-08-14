<?php

use Phinx\Migration\AbstractMigration;

class CreateMemberDataTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('member_data');

        $table->addColumn('member_id', 'integer');
        $table->addColumn('m_field_id_1', 'string', ['limit' => 100, 'null' => true]);
    
        $table->create();
    }
}
