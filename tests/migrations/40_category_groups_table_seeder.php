<?php

use Phinx\Migration\AbstractMigration;

class CategoryGroupsTableSeeder extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = $this->adapter->getConnection()->prepare('INSERT INTO category_groups (`group_id`, `site_id`, `group_name`, `sort_order`, `exclude_group`, `field_html_formatting`, `can_edit_categories`, `can_delete_categories`) VALUES (:group_id, :site_id, :group_name, :sort_order, :exclude_group, :field_html_formatting, :can_edit_categories, :can_delete_categories)');

        $query->execute([
            'group_id' => 1,
            'site_id' => 1,
            'group_name' => 'Group A',
            'sort_order' => 'a',
            'exclude_group' => 0,
            'field_html_formatting' => 'all',
            'can_edit_categories' => '',
            'can_delete_categories' => '',
        ]);

        $query->execute([
            'group_id' => 2,
            'site_id' => 1,
            'group_name' => 'Group B',
            'sort_order' => 'a',
            'exclude_group' => 0,
            'field_html_formatting' => 'all',
            'can_edit_categories' => '',
            'can_delete_categories' => '',
        ]);


    }

}
