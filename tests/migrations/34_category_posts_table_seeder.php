<?php

use Phinx\Migration\AbstractMigration;

class CategoryPostsTableSeeder extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = $this->adapter->getConnection()->prepare('INSERT INTO category_posts (`entry_id`, `cat_id`) VALUES (:entry_id, :cat_id)');

        $query->execute([
            'entry_id' => '7',
            'cat_id' => '1',
        ]);

        $query->execute([
            'entry_id' => '7',
            'cat_id' => '2',
        ]);

        $query->execute([
            'entry_id' => '9',
            'cat_id' => '1',
        ]);

        $query->execute([
            'entry_id' => '11',
            'cat_id' => '2',
        ]);


    }

}
