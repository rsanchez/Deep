<?php

use Phinx\Migration\AbstractMigration;

class MemberFieldsTableSeeder extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = $this->adapter->getConnection()->prepare('INSERT INTO member_fields (`m_field_id`, `m_field_name`, `m_field_label`, `m_field_description`, `m_field_type`, `m_field_list_items`, `m_field_ta_rows`, `m_field_maxl`, `m_field_width`, `m_field_search`, `m_field_required`, `m_field_public`, `m_field_reg`, `m_field_cp_reg`, `m_field_fmt`, `m_field_order`) VALUES (:m_field_id, :m_field_name, :m_field_label, :m_field_description, :m_field_type, :m_field_list_items, :m_field_ta_rows, :m_field_maxl, :m_field_width, :m_field_search, :m_field_required, :m_field_public, :m_field_reg, :m_field_cp_reg, :m_field_fmt, :m_field_order)');

        $query->execute([
            'm_field_id' => '1',
            'm_field_name' => 'member_country',
            'm_field_label' => 'Country',
            'm_field_description' => '',
            'm_field_type' => 'select',
            'm_field_list_items' => 'USA
CAN
MEX',
            'm_field_ta_rows' => '10',
            'm_field_maxl' => '100',
            'm_field_width' => '100%',
            'm_field_search' => 'y',
            'm_field_required' => 'n',
            'm_field_public' => 'y',
            'm_field_reg' => 'n',
            'm_field_cp_reg' => 'n',
            'm_field_fmt' => 'none',
            'm_field_order' => '1',
        ]);


    }

}
