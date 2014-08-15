<?php

use Phinx\Migration\AbstractMigration;

class AssetsFilesTableSeeder extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = $this->adapter->getConnection()->prepare('INSERT INTO assets_files (`file_id`, `folder_id`, `source_type`, `source_id`, `filedir_id`, `file_name`, `title`, `date`, `alt_text`, `caption`, `author`, `desc`, `location`, `keywords`, `date_modified`, `kind`, `width`, `height`, `size`, `search_keywords`) VALUES (:file_id, :folder_id, :source_type, :source_id, :filedir_id, :file_name, :title, :date, :alt_text, :caption, :author, :desc, :location, :keywords, :date_modified, :kind, :width, :height, :size, :search_keywords)');

        $query->execute([
            'file_id' => '1',
            'folder_id' => '1',
            'source_type' => 'ee',
            'source_id' => null,
            'filedir_id' => '1',
            'file_name' => '1eecbed0063a0253.jpg',
            'title' => null,
            'date' => '1399602051',
            'alt_text' => null,
            'caption' => null,
            'author' => null,
            'desc' => null,
            'location' => null,
            'keywords' => null,
            'date_modified' => '1399602051',
            'kind' => 'image',
            'width' => '506',
            'height' => '286',
            'size' => '1151',
            'search_keywords' => '1eecbed0063a0253.jpg',
        ]);

        $query->execute([
            'file_id' => '2',
            'folder_id' => '1',
            'source_type' => 'ee',
            'source_id' => null,
            'filedir_id' => '1',
            'file_name' => '22bb4d5d6211e00f.jpg',
            'title' => null,
            'date' => '1399602057',
            'alt_text' => null,
            'caption' => null,
            'author' => null,
            'desc' => null,
            'location' => null,
            'keywords' => null,
            'date_modified' => '1399602057',
            'kind' => 'image',
            'width' => '429',
            'height' => '283',
            'size' => '1015',
            'search_keywords' => '22bb4d5d6211e00f.jpg',
        ]);

        $query->execute([
            'file_id' => '3',
            'folder_id' => '1',
            'source_type' => 'ee',
            'source_id' => null,
            'filedir_id' => '1',
            'file_name' => '492f2c6f0795b583.jpg',
            'title' => null,
            'date' => '1399602058',
            'alt_text' => null,
            'caption' => null,
            'author' => null,
            'desc' => null,
            'location' => null,
            'keywords' => null,
            'date_modified' => '1399602058',
            'kind' => 'image',
            'width' => '353',
            'height' => '173',
            'size' => '667',
            'search_keywords' => '492f2c6f0795b583.jpg',
        ]);

        $query->execute([
            'file_id' => '4',
            'folder_id' => '1',
            'source_type' => 'ee',
            'source_id' => null,
            'filedir_id' => '1',
            'file_name' => 'b87ba69c47a83184.jpg',
            'title' => null,
            'date' => '1399602057',
            'alt_text' => null,
            'caption' => null,
            'author' => null,
            'desc' => null,
            'location' => null,
            'keywords' => null,
            'date_modified' => '1399602057',
            'kind' => 'image',
            'width' => '349',
            'height' => '191',
            'size' => '683',
            'search_keywords' => 'b87ba69c47a83184.jpg',
        ]);

        $query->execute([
            'file_id' => '5',
            'folder_id' => '1',
            'source_type' => 'ee',
            'source_id' => null,
            'filedir_id' => '1',
            'file_name' => 'c07cd109414fa275.jpg',
            'title' => null,
            'date' => '1399602058',
            'alt_text' => null,
            'caption' => null,
            'author' => null,
            'desc' => null,
            'location' => null,
            'keywords' => null,
            'date_modified' => '1399602058',
            'kind' => 'image',
            'width' => '584',
            'height' => '291',
            'size' => '1342',
            'search_keywords' => 'c07cd109414fa275.jpg',
        ]);

        $query->execute([
            'file_id' => '6',
            'folder_id' => '2',
            'source_type' => 'ee',
            'source_id' => null,
            'filedir_id' => '1',
            'file_name' => 'fbc59e86a565f8a3.jpg',
            'title' => null,
            'date' => '1399602058',
            'alt_text' => null,
            'caption' => null,
            'author' => null,
            'desc' => null,
            'location' => null,
            'keywords' => null,
            'date_modified' => '1399602058',
            'kind' => 'image',
            'width' => '481',
            'height' => '582',
            'size' => '2008',
            'search_keywords' => 'fbc59e86a565f8a3.jpg',
        ]);


    }

}
