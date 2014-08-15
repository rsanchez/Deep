<?php

use Phinx\Migration\AbstractMigration;

class CreateMemberFieldsTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('member_fields');

        $table->addColumn('m_field_id', 'integer');
        $table->addColumn('m_field_name', 'string', ['limit' => 32]);
        $table->addColumn('m_field_label', 'string', ['limit' => 50]);
        $table->addColumn('m_field_description', 'text', ['limit' => 65535]);
        $table->addColumn('m_field_type', 'string', ['limit' => 12]);
        $table->addColumn('m_field_list_items', 'text', ['limit' => 65535]);
        $table->addColumn('m_field_ta_rows', 'boolean', ['null' => true]);
        $table->addColumn('m_field_maxl', 'integer');
        $table->addColumn('m_field_width', 'string', ['limit' => 6]);
        $table->addColumn('m_field_search', 'string', ['limit' => 1]);
        $table->addColumn('m_field_required', 'string', ['limit' => 1]);
        $table->addColumn('m_field_public', 'string', ['limit' => 1]);
        $table->addColumn('m_field_reg', 'string', ['limit' => 1]);
        $table->addColumn('m_field_cp_reg', 'string', ['limit' => 1]);
        $table->addColumn('m_field_fmt', 'string', ['limit' => 5]);
        $table->addColumn('m_field_order', 'integer');
    
        $table->create();
    }
}
