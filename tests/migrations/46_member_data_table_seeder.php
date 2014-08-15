<?php

use Phinx\Migration\AbstractMigration;

class MemberDataTableSeeder extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = $this->adapter->getConnection()->prepare('INSERT INTO member_data (`member_id`, `m_field_id_1`) VALUES (:member_id, :m_field_id_1)');

        $query->execute([
            'member_id' => '1',
            'm_field_id_1' => 'USA',
        ]);

        $query->execute([
            'member_id' => '2',
            'm_field_id_1' => null,
        ]);


    }

}
