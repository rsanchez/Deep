<?php

use Phinx\Migration\AbstractMigration;

class AssetsFoldersTableSeeder extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = $this->adapter->getConnection()->prepare('INSERT INTO assets_folders (`folder_id`, `source_type`, `folder_name`, `full_path`, `parent_id`, `source_id`, `filedir_id`) VALUES (:folder_id, :source_type, :folder_name, :full_path, :parent_id, :source_id, :filedir_id)');

        $query->execute([
            'folder_id' => '1',
            'source_type' => 'ee',
            'folder_name' => 'Uploads',
            'full_path' => '',
            'parent_id' => null,
            'source_id' => null,
            'filedir_id' => '1',
        ]);

        $query->execute([
            'folder_id' => '2',
            'source_type' => 'ee',
            'folder_name' => 'subfolder',
            'full_path' => 'subfolder/',
            'parent_id' => '1',
            'source_id' => null,
            'filedir_id' => '1',
        ]);


    }

}
