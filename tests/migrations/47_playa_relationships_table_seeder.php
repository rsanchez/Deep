<?php

use Phinx\Migration\AbstractMigration;

class PlayaRelationshipsTableSeeder extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = $this->adapter->getConnection()->prepare('INSERT INTO playa_relationships (`rel_id`, `parent_entry_id`, `parent_field_id`, `parent_col_id`, `parent_row_id`, `parent_var_id`, `parent_is_draft`, `child_entry_id`, `rel_order`) VALUES (:rel_id, :parent_entry_id, :parent_field_id, :parent_col_id, :parent_row_id, :parent_var_id, :parent_is_draft, :child_entry_id, :rel_order)');

        $query->execute([
            'rel_id' => '26',
            'parent_entry_id' => '7',
            'parent_field_id' => '13',
            'parent_col_id' => '3',
            'parent_row_id' => '1',
            'parent_var_id' => null,
            'parent_is_draft' => '0',
            'child_entry_id' => '4',
            'rel_order' => '1',
        ]);

        $query->execute([
            'rel_id' => '25',
            'parent_entry_id' => '7',
            'parent_field_id' => '13',
            'parent_col_id' => '3',
            'parent_row_id' => '1',
            'parent_var_id' => null,
            'parent_is_draft' => '0',
            'child_entry_id' => '2',
            'rel_order' => '0',
        ]);

        $query->execute([
            'rel_id' => '28',
            'parent_entry_id' => '7',
            'parent_field_id' => '15',
            'parent_col_id' => null,
            'parent_row_id' => null,
            'parent_var_id' => null,
            'parent_is_draft' => '0',
            'child_entry_id' => '4',
            'rel_order' => '1',
        ]);

        $query->execute([
            'rel_id' => '27',
            'parent_entry_id' => '7',
            'parent_field_id' => '15',
            'parent_col_id' => null,
            'parent_row_id' => null,
            'parent_var_id' => null,
            'parent_is_draft' => '0',
            'child_entry_id' => '2',
            'rel_order' => '0',
        ]);

        $query->execute([
            'rel_id' => '33',
            'parent_entry_id' => '8',
            'parent_field_id' => '13',
            'parent_col_id' => '3',
            'parent_row_id' => '2',
            'parent_var_id' => null,
            'parent_is_draft' => '0',
            'child_entry_id' => '6',
            'rel_order' => '0',
        ]);

        $query->execute([
            'rel_id' => '34',
            'parent_entry_id' => '8',
            'parent_field_id' => '13',
            'parent_col_id' => '3',
            'parent_row_id' => '3',
            'parent_var_id' => null,
            'parent_is_draft' => '0',
            'child_entry_id' => '4',
            'rel_order' => '0',
        ]);

        $query->execute([
            'rel_id' => '36',
            'parent_entry_id' => '8',
            'parent_field_id' => '15',
            'parent_col_id' => null,
            'parent_row_id' => null,
            'parent_var_id' => null,
            'parent_is_draft' => '0',
            'child_entry_id' => '6',
            'rel_order' => '1',
        ]);

        $query->execute([
            'rel_id' => '35',
            'parent_entry_id' => '8',
            'parent_field_id' => '15',
            'parent_col_id' => null,
            'parent_row_id' => null,
            'parent_var_id' => null,
            'parent_is_draft' => '0',
            'child_entry_id' => '4',
            'rel_order' => '0',
        ]);


    }

}
