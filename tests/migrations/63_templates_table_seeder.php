<?php

use Phinx\Migration\AbstractMigration;

class TemplatesTableSeeder extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = $this->adapter->getConnection()->prepare('INSERT INTO templates (`template_id`, `site_id`, `group_id`, `template_name`, `save_template_file`, `template_type`, `template_data`, `template_notes`, `edit_date`, `last_author_id`, `cache`, `refresh`, `no_auth_bounce`, `enable_http_auth`, `allow_php`, `php_parse_location`, `hits`) VALUES (:template_id, :site_id, :group_id, :template_name, :save_template_file, :template_type, :template_data, :template_notes, :edit_date, :last_author_id, :cache, :refresh, :no_auth_bounce, :enable_http_auth, :allow_php, :php_parse_location, :hits)');

        $query->execute([
            'template_id' => 1,
            'site_id' => 1,
            'group_id' => 1,
            'template_name' => 'index',
            'save_template_file' => 'y',
            'template_type' => 'webpage',
            'template_data' => '',
            'template_notes' => '',
            'edit_date' => 1399513712,
            'last_author_id' => 1,
            'cache' => 'n',
            'refresh' => 0,
            'no_auth_bounce' => '',
            'enable_http_auth' => 'n',
            'allow_php' => 'n',
            'php_parse_location' => 'o',
            'hits' => 40,
        ]);


    }

}
