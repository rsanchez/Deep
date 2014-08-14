<?php

use Phinx\Migration\AbstractMigration;

class CategoryFieldDataTableSeeder extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = $this->adapter->getConnection()->prepare('INSERT INTO category_field_data (`cat_id`, `site_id`, `group_id`, `field_id_1`, `field_ft_1`) VALUES (:cat_id, :site_id, :group_id, :field_id_1, :field_ft_1)');

        $query->execute([
            'cat_id' => '1',
            'site_id' => '1',
            'group_id' => '1',
            'field_id_1' => 'Red',
            'field_ft_1' => 'none',
        ]);

        $query->execute([
            'cat_id' => '2',
            'site_id' => '1',
            'group_id' => '1',
            'field_id_1' => 'Blue',
            'field_ft_1' => 'none',
        ]);

        $query->execute([
            'cat_id' => '3',
            'site_id' => '1',
            'group_id' => '2',
            'field_id_1' => null,
            'field_ft_1' => 'none',
        ]);


    }

}
