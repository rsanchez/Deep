<?php

use Phinx\Migration\AbstractMigration;

class CreateCommentsTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('comments');

        $table->addColumn('comment_id', 'integer');
        $table->addColumn('site_id', 'integer', ['null' => true]);
        $table->addColumn('entry_id', 'integer', ['null' => true]);
        $table->addColumn('channel_id', 'integer', ['null' => true]);
        $table->addColumn('author_id', 'integer', ['null' => true]);
        $table->addColumn('status', 'string', ['limit' => 1, 'null' => true]);
        $table->addColumn('name', 'string', ['limit' => 50, 'null' => true]);
        $table->addColumn('email', 'string', ['limit' => 75, 'null' => true]);
        $table->addColumn('url', 'string', ['limit' => 75, 'null' => true]);
        $table->addColumn('location', 'string', ['limit' => 50, 'null' => true]);
        $table->addColumn('ip_address', 'string', ['limit' => 45, 'null' => true]);
        $table->addColumn('comment_date', 'integer', ['null' => true]);
        $table->addColumn('edit_date', 'integer', ['null' => true]);
        $table->addColumn('comment', 'text', ['limit' => 65535, 'null' => true]);
    
        $table->create();
    }
}
