<?php

use Phinx\Migration\AbstractMigration;

class CategoriesTableSeeder extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = $this->adapter->getConnection()->prepare('INSERT INTO categories (`cat_id`, `site_id`, `group_id`, `parent_id`, `cat_name`, `cat_url_title`, `cat_description`, `cat_image`, `cat_order`) VALUES (:cat_id, :site_id, :group_id, :parent_id, :cat_name, :cat_url_title, :cat_description, :cat_image, :cat_order)');

        $query->execute([
            'cat_id' => '1',
            'site_id' => '1',
            'group_id' => '1',
            'parent_id' => '0',
            'cat_name' => 'Category A',
            'cat_url_title' => 'category-a',
            'cat_description' => 'Description A',
            'cat_image' => '',
            'cat_order' => '1',
        ]);

        $query->execute([
            'cat_id' => '2',
            'site_id' => '1',
            'group_id' => '1',
            'parent_id' => '0',
            'cat_name' => 'Category B',
            'cat_url_title' => 'category-b',
            'cat_description' => 'Descrption B',
            'cat_image' => '',
            'cat_order' => '2',
        ]);

        $query->execute([
            'cat_id' => '3',
            'site_id' => '1',
            'group_id' => '2',
            'parent_id' => '0',
            'cat_name' => 'Category C',
            'cat_url_title' => 'category-c',
            'cat_description' => '',
            'cat_image' => '',
            'cat_order' => '1',
        ]);


    }

}
