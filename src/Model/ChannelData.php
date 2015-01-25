<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

/**
 * Model for the channel_data table
 */
class ChannelData extends Model
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'channel_data';

    /**
     * {@inheritdoc}
     */
    protected $primaryKey = 'entry_id';

    /**
     * {@inheritdoc}
     */
    protected $rules = [
        'site_id' => 'required|exists:sites,site_id',
        'channel_id' => 'required|exists:channels,channel_id',
        'entry_id' => 'required|exists:channel_titles,entry_id',
    ];
}
