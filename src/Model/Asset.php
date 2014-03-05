<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use rsanchez\Deep\Model\FileInterface;
use rsanchez\Deep\Collection\AssetCollection;

/**
 * Model for the assets_files table, joined with assets_selections
 */
class Asset extends Model implements FileInterface
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $table = 'assets_files';

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $primaryKey = 'file_id';

    /**
     * {@inheritdoc}
     */
    protected $hidden = array('file_id', 'folder_id', 'source_type', 'source_id', 'filedir_id', 'entry_id', 'field_id', 'col_id', 'row_id', 'var_id', 'element_id', 'content_type', 'sort_order', 'is_draft', 'uploadPref');

    /**
     * {@inheritdoc}
     */
    protected $appends = array('url');

    /**
     * Define the Upload Preferences Eloquent relationship
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function uploadPref()
    {
        return $this->hasOne('\\rsanchez\\Deep\\Model\\UploadPref', 'id', 'filedir_id');
    }

    /**
     * {@inheritdoc}
     *
     * @param  array                                     $assets
     * @return \rsanchez\Deep\Collection\AssetCollection
     */
    public function newCollection(array $assets = array())
    {
        return new AssetCollection($assets);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlAttribute()
    {
        return $this->uploadPref->url.$this->file_name;
    }

    /**
     * {@inheritdoc}
     */
    public function getServerPathAttribute()
    {
        return $this->uploadPref->server_path.$this->file_name;
    }

    /**
     * Filter by Entry ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string|array                          $entryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEntryId(Builder $query, $entryId)
    {
        $entryId = is_array($entryId) ? $entryId : array($entryId);

        return $this->requireTable($query, 'assets_selections')->whereIn('assets_selections.entry_id', $entryId);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getUrlAttribute();
    }

    /**
     * Join the required table, once
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $which table name
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    protected function requireTable(Builder $query, $which)
    {
        static $tables = array(
            'assets_selections' => array('assets_selections.file_id', 'assets_files.file_id'),
        );

        if (! isset($tables[$which])) {
            return $query;
        }

        if (isset($query->getQuery()->joins)) {
            foreach ($query->getQuery()->joins as $joinClause) {
                if ($joinClause->table === $which) {
                    return $query;
                }
            }
        }

        return $query->join($which, $tables[$which][0], '=', $tables[$which][1]);
    }
}
