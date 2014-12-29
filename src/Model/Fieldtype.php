<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

/**
 * Model for the fieldtypes table
 */
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
