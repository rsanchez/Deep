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
 * Model for the sites table
 */
class Site extends Model
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'sites';

    /**
     * {@inheritdoc}
     */
    protected $primaryKey = 'site_id';

    /**
     * Get the system preferences for this site
     * @return array|null
     */
    public function getSiteSystemPreferencesAttribute($value)
    {
        return $value ? @unserialize(@base64_decode($value)) : $value;
    }

    /**
     * Get the member preferences for this site
     * @return array|null
     */
    public function getSiteMemberPreferencesAttribute($value)
    {
        return $value ? @unserialize(@base64_decode($value)) : $value;
    }

    /**
     * Get the template preferences for this site
     * @return array|null
     */
    public function getSiteTemplatePreferencesAttribute($value)
    {
        return $value ? @unserialize(@base64_decode($value)) : $value;
    }
}
