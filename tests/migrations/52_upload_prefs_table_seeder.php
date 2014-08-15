<?php

use Phinx\Migration\AbstractMigration;

class UploadPrefsTableSeeder extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = $this->adapter->getConnection()->prepare('INSERT INTO upload_prefs (`id`, `site_id`, `name`, `server_path`, `url`, `allowed_types`, `max_size`, `max_height`, `max_width`, `properties`, `pre_format`, `post_format`, `file_properties`, `file_pre_format`, `file_post_format`, `cat_group`, `batch_location`) VALUES (:id, :site_id, :name, :server_path, :url, :allowed_types, :max_size, :max_height, :max_width, :properties, :pre_format, :post_format, :file_properties, :file_pre_format, :file_post_format, :cat_group, :batch_location)');

        $query->execute([
            'id' => '1',
            'site_id' => '1',
            'name' => 'Uploads',
            'server_path' => './uploads/',
            'url' => '/uploads/',
            'allowed_types' => 'all',
            'max_size' => '',
            'max_height' => '',
            'max_width' => '',
            'properties' => '',
            'pre_format' => '',
            'post_format' => '',
            'file_properties' => '',
            'file_pre_format' => '',
            'file_post_format' => '',
            'cat_group' => '',
            'batch_location' => null,
        ]);


    }

}
