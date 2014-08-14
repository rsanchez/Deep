<?php

use Phinx\Migration\AbstractMigration;

class RelationshipsTableSeeder extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = $this->adapter->getConnection()->prepare('INSERT INTO relationships (`relationship_id`, `parent_id`, `child_id`, `field_id`, `grid_field_id`, `grid_col_id`, `grid_row_id`, `order`) VALUES (:relationship_id, :parent_id, :child_id, :field_id, :grid_field_id, :grid_col_id, :grid_row_id, :order)');

        $query->execute([
            'relationship_id' => '26',
            'parent_id' => '7',
            'child_id' => '3',
            'field_id' => '3',
            'grid_field_id' => '12',
            'grid_col_id' => '3',
            'grid_row_id' => '1',
            'order' => '2',
        ]);

        $query->execute([
            'relationship_id' => '25',
            'parent_id' => '7',
            'child_id' => '1',
            'field_id' => '3',
            'grid_field_id' => '12',
            'grid_col_id' => '3',
            'grid_row_id' => '1',
            'order' => '1',
        ]);

        $query->execute([
            'relationship_id' => '28',
            'parent_id' => '7',
            'child_id' => '3',
            'field_id' => '17',
            'grid_field_id' => '0',
            'grid_col_id' => '0',
            'grid_row_id' => '0',
            'order' => '2',
        ]);

        $query->execute([
            'relationship_id' => '27',
            'parent_id' => '7',
            'child_id' => '1',
            'field_id' => '17',
            'grid_field_id' => '0',
            'grid_col_id' => '0',
            'grid_row_id' => '0',
            'order' => '1',
        ]);

        $query->execute([
            'relationship_id' => '33',
            'parent_id' => '8',
            'child_id' => '5',
            'field_id' => '3',
            'grid_field_id' => '12',
            'grid_col_id' => '3',
            'grid_row_id' => '2',
            'order' => '1',
        ]);

        $query->execute([
            'relationship_id' => '34',
            'parent_id' => '8',
            'child_id' => '3',
            'field_id' => '3',
            'grid_field_id' => '12',
            'grid_col_id' => '3',
            'grid_row_id' => '3',
            'order' => '1',
        ]);

        $query->execute([
            'relationship_id' => '36',
            'parent_id' => '8',
            'child_id' => '5',
            'field_id' => '17',
            'grid_field_id' => '0',
            'grid_col_id' => '0',
            'grid_row_id' => '0',
            'order' => '2',
        ]);

        $query->execute([
            'relationship_id' => '35',
            'parent_id' => '8',
            'child_id' => '3',
            'field_id' => '17',
            'grid_field_id' => '0',
            'grid_col_id' => '0',
            'grid_row_id' => '0',
            'order' => '1',
        ]);


    }

}
