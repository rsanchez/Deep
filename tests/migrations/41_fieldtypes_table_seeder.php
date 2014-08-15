<?php

use Phinx\Migration\AbstractMigration;

class FieldtypesTableSeeder extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = $this->adapter->getConnection()->prepare('INSERT INTO fieldtypes (`fieldtype_id`, `name`, `version`, `settings`, `has_global_settings`) VALUES (:fieldtype_id, :name, :version, :settings, :has_global_settings)');

        $query->execute([
            'fieldtype_id' => '1',
            'name' => 'select',
            'version' => '1.0',
            'settings' => 'YTowOnt9',
            'has_global_settings' => 'n',
        ]);

        $query->execute([
            'fieldtype_id' => '2',
            'name' => 'text',
            'version' => '1.0',
            'settings' => 'YTowOnt9',
            'has_global_settings' => 'n',
        ]);

        $query->execute([
            'fieldtype_id' => '3',
            'name' => 'textarea',
            'version' => '1.0',
            'settings' => 'YTowOnt9',
            'has_global_settings' => 'n',
        ]);

        $query->execute([
            'fieldtype_id' => '4',
            'name' => 'date',
            'version' => '1.0',
            'settings' => 'YTowOnt9',
            'has_global_settings' => 'n',
        ]);

        $query->execute([
            'fieldtype_id' => '5',
            'name' => 'file',
            'version' => '1.0',
            'settings' => 'YTowOnt9',
            'has_global_settings' => 'n',
        ]);

        $query->execute([
            'fieldtype_id' => '7',
            'name' => 'multi_select',
            'version' => '1.0',
            'settings' => 'YTowOnt9',
            'has_global_settings' => 'n',
        ]);

        $query->execute([
            'fieldtype_id' => '8',
            'name' => 'checkboxes',
            'version' => '1.0',
            'settings' => 'YTowOnt9',
            'has_global_settings' => 'n',
        ]);

        $query->execute([
            'fieldtype_id' => '9',
            'name' => 'radio',
            'version' => '1.0',
            'settings' => 'YTowOnt9',
            'has_global_settings' => 'n',
        ]);

        $query->execute([
            'fieldtype_id' => '10',
            'name' => 'relationship',
            'version' => '1.0',
            'settings' => 'YTowOnt9',
            'has_global_settings' => 'n',
        ]);

        $query->execute([
            'fieldtype_id' => '11',
            'name' => 'rte',
            'version' => '1.0',
            'settings' => 'YTowOnt9',
            'has_global_settings' => 'n',
        ]);

        $query->execute([
            'fieldtype_id' => '12',
            'name' => 'assets',
            'version' => '2.3.2',
            'settings' => 'YTowOnt9',
            'has_global_settings' => 'y',
        ]);

        $query->execute([
            'fieldtype_id' => '13',
            'name' => 'fieldpack_checkboxes',
            'version' => '2.1.1',
            'settings' => 'YTowOnt9',
            'has_global_settings' => 'n',
        ]);

        $query->execute([
            'fieldtype_id' => '14',
            'name' => 'fieldpack_dropdown',
            'version' => '2.1.1',
            'settings' => 'YTowOnt9',
            'has_global_settings' => 'n',
        ]);

        $query->execute([
            'fieldtype_id' => '15',
            'name' => 'fieldpack_list',
            'version' => '2.1.1',
            'settings' => 'YTowOnt9',
            'has_global_settings' => 'n',
        ]);

        $query->execute([
            'fieldtype_id' => '16',
            'name' => 'fieldpack_multiselect',
            'version' => '2.1.1',
            'settings' => 'YTowOnt9',
            'has_global_settings' => 'n',
        ]);

        $query->execute([
            'fieldtype_id' => '17',
            'name' => 'fieldpack_pill',
            'version' => '2.1.1',
            'settings' => 'YTowOnt9',
            'has_global_settings' => 'n',
        ]);

        $query->execute([
            'fieldtype_id' => '18',
            'name' => 'fieldpack_radio_buttons',
            'version' => '2.1.1',
            'settings' => 'YTowOnt9',
            'has_global_settings' => 'n',
        ]);

        $query->execute([
            'fieldtype_id' => '19',
            'name' => 'fieldpack_switch',
            'version' => '2.1.1',
            'settings' => 'YTowOnt9',
            'has_global_settings' => 'n',
        ]);

        $query->execute([
            'fieldtype_id' => '20',
            'name' => 'grid',
            'version' => '1.0',
            'settings' => 'YTowOnt9',
            'has_global_settings' => 'n',
        ]);

        $query->execute([
            'fieldtype_id' => '21',
            'name' => 'matrix',
            'version' => '2.5.10',
            'settings' => 'YTowOnt9',
            'has_global_settings' => 'y',
        ]);

        $query->execute([
            'fieldtype_id' => '22',
            'name' => 'playa',
            'version' => '4.4.5',
            'settings' => 'YTowOnt9',
            'has_global_settings' => 'y',
        ]);

        $query->execute([
            'fieldtype_id' => '23',
            'name' => 'wygwam',
            'version' => '3.3',
            'settings' => 'YToyOntzOjExOiJsaWNlbnNlX2tleSI7czowOiIiO3M6MTI6ImZpbGVfYnJvd3NlciI7czo2OiJhc3NldHMiO30=',
            'has_global_settings' => 'y',
        ]);


    }

}
