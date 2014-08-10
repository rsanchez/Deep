<?php

use Phinx\Migration\AbstractMigration;

class CreateChannelTitlesTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('channel_titles', ['id' => 'entry_id']);

        $table->addColumn('site_id', 'integer', ['signed' => false]);
        $table->addColumn('channel_id', 'integer', ['signed' => false]);
        $table->addColumn('author_id', 'integer', ['signed' => false]);
        $table->addColumn('forum_topic_id', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('ip_address', 'string', ['limit' => 45]);
        $table->addColumn('title', 'string', ['limit' => 100]);
        $table->addColumn('url_title', 'string', ['limit' => 75]);
        $table->addColumn('status', 'string', ['limit' => 50]);
        $table->addColumn('versioning_enabled', 'string', ['limit' => 1]);
        $table->addColumn('view_count_one', 'integer', ['signed' => false]);
        $table->addColumn('view_count_two', 'integer', ['signed' => false]);
        $table->addColumn('view_count_three', 'integer', ['signed' => false]);
        $table->addColumn('view_count_four', 'integer', ['signed' => false]);
        $table->addColumn('allow_comments', 'string', ['limit' => 1]);
        $table->addColumn('sticky', 'string', ['limit' => 1]);
        $table->addColumn('entry_date', 'integer');
        $table->addColumn('year', 'string', ['limit' => 4]);
        $table->addColumn('month', 'string', ['limit' => 2]);
        $table->addColumn('day', 'string', ['limit' => 3]);
        $table->addColumn('expiration_date', 'integer');
        $table->addColumn('comment_expiration_date', 'integer');
        $table->addColumn('edit_date', 'integer', ['null' => true]);
        $table->addColumn('recent_comment_date', 'integer', ['null' => true]);
        $table->addColumn('comment_total', 'integer', ['signed' => false]);
    
        $table->create();
    }
}
