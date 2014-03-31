<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for the members table
 */
class Member extends Model
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $table = 'members';

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $primaryKey = 'member_id';

    /**
     * Return the screen_name when cast to string
     *
     * @var string
     */
    public function __toString()
    {
        return $this->screen_name;
    }
}
