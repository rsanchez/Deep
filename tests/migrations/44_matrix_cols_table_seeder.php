<?php

use Phinx\Migration\AbstractMigration;

class MatrixColsTableSeeder extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = $this->adapter->getConnection()->prepare('INSERT INTO matrix_cols (`col_id`, `site_id`, `field_id`, `var_id`, `col_name`, `col_label`, `col_instructions`, `col_type`, `col_required`, `col_search`, `col_order`, `col_width`, `col_settings`) VALUES (:col_id, :site_id, :field_id, :var_id, :col_name, :col_label, :col_instructions, :col_type, :col_required, :col_search, :col_order, :col_width, :col_settings)');

        $query->execute([
            'col_id' => '1',
            'site_id' => '1',
            'field_id' => '13',
            'var_id' => null,
            'col_name' => 'text',
            'col_label' => 'Text',
            'col_instructions' => '',
            'col_type' => 'text',
            'col_required' => 'n',
            'col_search' => 'n',
            'col_order' => '0',
            'col_width' => '33%',
            'col_settings' => 'YTozOntzOjQ6Im1heGwiO3M6MDoiIjtzOjM6ImZtdCI7czo0OiJub25lIjtzOjM6ImRpciI7czozOiJsdHIiO30=',
        ]);

        $query->execute([
            'col_id' => '2',
            'site_id' => '1',
            'field_id' => '13',
            'var_id' => null,
            'col_name' => 'file',
            'col_label' => 'File',
            'col_instructions' => '',
            'col_type' => 'file',
            'col_required' => 'n',
            'col_search' => 'n',
            'col_order' => '1',
            'col_width' => '',
            'col_settings' => 'YTozOntzOjk6ImRpcmVjdG9yeSI7czozOiJhbGwiO3M6MTI6ImNvbnRlbnRfdHlwZSI7czozOiJhbGwiO3M6MTc6ImZpbGVfbnVtX2V4aXN0aW5nIjtzOjA6IiI7fQ==',
        ]);

        $query->execute([
            'col_id' => '3',
            'site_id' => '1',
            'field_id' => '13',
            'var_id' => null,
            'col_name' => 'playa',
            'col_label' => 'Playa',
            'col_instructions' => '',
            'col_type' => 'playa',
            'col_required' => 'n',
            'col_search' => 'n',
            'col_order' => '2',
            'col_width' => '',
            'col_settings' => 'YTo2OntzOjU6Im11bHRpIjtzOjE6InkiO3M6NzoiZXhwaXJlZCI7czoxOiJuIjtzOjY6ImZ1dHVyZSI7czoxOiJ5IjtzOjg6ImVkaXRhYmxlIjtzOjE6Im4iO3M6Nzoib3JkZXJieSI7czo1OiJ0aXRsZSI7czo0OiJzb3J0IjtzOjM6IkFTQyI7fQ==',
        ]);


    }

}
