<?php

use Phinx\Migration\AbstractMigration;

class CreateCategoryFieldsTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('category_fields');

        $table->addColumn('field_id', 'integer');
        $table->addColumn('site_id', 'integer');
        $table->addColumn('group_id', 'integer');
        $table->addColumn('field_name', 'string', ['limit' => 32]);
        $table->addColumn('field_label', 'string', ['limit' => 50]);
        $table->addColumn('field_type', 'string', ['limit' => 12]);
        $table->addColumn('field_list_items', 'text', ['limit' => 65535]);
        $table->addColumn('field_maxl', 'integer');
        $table->addColumn('field_ta_rows', 'boolean');
        $table->addColumn('field_default_fmt', 'string', ['limit' => 40]);
        $table->addColumn('field_show_fmt', 'string', ['limit' => 1]);
        $table->addColumn('field_text_direction', 'string', ['limit' => 3]);
        $table->addColumn('field_required', 'string', ['limit' => 1]);
        $table->addColumn('field_order', 'integer');
    
        $table->create();
    }
}
