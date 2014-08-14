<?php

use Phinx\Migration\AbstractMigration;

class CreateChannelFieldsTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('channel_fields');

        $table->addColumn('field_id', 'integer');
        $table->addColumn('site_id', 'integer');
        $table->addColumn('group_id', 'integer');
        $table->addColumn('field_name', 'string', ['limit' => 32]);
        $table->addColumn('field_label', 'string', ['limit' => 50]);
        $table->addColumn('field_instructions', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('field_type', 'string', ['limit' => 50]);
        $table->addColumn('field_list_items', 'text', ['limit' => 65535]);
        $table->addColumn('field_pre_populate', 'string', ['limit' => 1]);
        $table->addColumn('field_pre_channel_id', 'integer', ['null' => true]);
        $table->addColumn('field_pre_field_id', 'integer', ['null' => true]);
        $table->addColumn('field_ta_rows', 'boolean', ['null' => true]);
        $table->addColumn('field_maxl', 'integer', ['null' => true]);
        $table->addColumn('field_required', 'string', ['limit' => 1]);
        $table->addColumn('field_text_direction', 'string', ['limit' => 3]);
        $table->addColumn('field_search', 'string', ['limit' => 1]);
        $table->addColumn('field_is_hidden', 'string', ['limit' => 1]);
        $table->addColumn('field_fmt', 'string', ['limit' => 40]);
        $table->addColumn('field_show_fmt', 'string', ['limit' => 1]);
        $table->addColumn('field_order', 'integer');
        $table->addColumn('field_content_type', 'string', ['limit' => 20]);
        $table->addColumn('field_settings', 'text', ['limit' => 65535, 'null' => true]);
    
        $table->create();
    }
}
