<?php

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model;

class Fieldtype extends Model
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $table = 'fieldtypes';

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $primaryKey = 'fieldtype_id';
}
