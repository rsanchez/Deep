<?php

use Phinx\Migration\AbstractMigration;

class FilesTableSeeder extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = $this->adapter->getConnection()->prepare('INSERT INTO files (`file_id`, `site_id`, `title`, `upload_location_id`, `rel_path`, `mime_type`, `file_name`, `file_size`, `description`, `credit`, `location`, `uploaded_by_member_id`, `upload_date`, `modified_by_member_id`, `modified_date`, `file_hw_original`) VALUES (:file_id, :site_id, :title, :upload_location_id, :rel_path, :mime_type, :file_name, :file_size, :description, :credit, :location, :uploaded_by_member_id, :upload_date, :modified_by_member_id, :modified_date, :file_hw_original)');

        $query->execute([
            'file_id' => '1',
            'site_id' => '1',
            'title' => '1eecbed0063a0253.jpg',
            'upload_location_id' => '1',
            'rel_path' => '/Users/robsanchez/Sites/deep-ee/uploads/1eecbed0063a0253.jpg',
            'mime_type' => 'image/jpeg',
            'file_name' => '1eecbed0063a0253.jpg',
            'file_size' => '1151',
            'description' => null,
            'credit' => null,
            'location' => null,
            'uploaded_by_member_id' => '1',
            'upload_date' => '1399602051',
            'modified_by_member_id' => '1',
            'modified_date' => '1399602051',
            'file_hw_original' => '286 506',
        ]);

        $query->execute([
            'file_id' => '2',
            'site_id' => '1',
            'title' => '22bb4d5d6211e00f.jpg',
            'upload_location_id' => '1',
            'rel_path' => '/Users/robsanchez/Sites/deep-ee/uploads/22bb4d5d6211e00f.jpg',
            'mime_type' => 'image/jpeg',
            'file_name' => '22bb4d5d6211e00f.jpg',
            'file_size' => '1015',
            'description' => null,
            'credit' => null,
            'location' => null,
            'uploaded_by_member_id' => '1',
            'upload_date' => '1399602057',
            'modified_by_member_id' => '1',
            'modified_date' => '1399602057',
            'file_hw_original' => '283 429',
        ]);

        $query->execute([
            'file_id' => '3',
            'site_id' => '1',
            'title' => '492f2c6f0795b583.jpg',
            'upload_location_id' => '1',
            'rel_path' => '/Users/robsanchez/Sites/deep-ee/uploads/492f2c6f0795b583.jpg',
            'mime_type' => 'image/jpeg',
            'file_name' => '492f2c6f0795b583.jpg',
            'file_size' => '667',
            'description' => null,
            'credit' => null,
            'location' => null,
            'uploaded_by_member_id' => '1',
            'upload_date' => '1399602057',
            'modified_by_member_id' => '1',
            'modified_date' => '1399602057',
            'file_hw_original' => '173 353',
        ]);

        $query->execute([
            'file_id' => '4',
            'site_id' => '1',
            'title' => 'b87ba69c47a83184.jpg',
            'upload_location_id' => '1',
            'rel_path' => '/Users/robsanchez/Sites/deep-ee/uploads/b87ba69c47a83184.jpg',
            'mime_type' => 'image/jpeg',
            'file_name' => 'b87ba69c47a83184.jpg',
            'file_size' => '683',
            'description' => null,
            'credit' => null,
            'location' => null,
            'uploaded_by_member_id' => '1',
            'upload_date' => '1399602057',
            'modified_by_member_id' => '1',
            'modified_date' => '1399602057',
            'file_hw_original' => '191 349',
        ]);

        $query->execute([
            'file_id' => '5',
            'site_id' => '1',
            'title' => 'c07cd109414fa275.jpg',
            'upload_location_id' => '1',
            'rel_path' => '/Users/robsanchez/Sites/deep-ee/uploads/c07cd109414fa275.jpg',
            'mime_type' => 'image/jpeg',
            'file_name' => 'c07cd109414fa275.jpg',
            'file_size' => '1342',
            'description' => null,
            'credit' => null,
            'location' => null,
            'uploaded_by_member_id' => '1',
            'upload_date' => '1399602058',
            'modified_by_member_id' => '1',
            'modified_date' => '1399602058',
            'file_hw_original' => '291 584',
        ]);


    }

}
