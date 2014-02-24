<?php

namespace rsanchez\Deep\Model\Collection;

use Illuminate\Database\Eloquent\Collection;

class EntryCollection extends Collection
{
    protected $matrixCols;
    protected $gridCols;

    protected static $hydrators = array(
        'matrix'       => '\\rsanchez\\Deep\\Model\\Hydrator\\MatrixHydrator',
        'grid'         => '\\rsanchez\\Deep\\Model\\Hydrator\\GridHydrator',
        'playa'        => '\\rsanchez\\Deep\\Model\\Hydrator\\PlayaHydrator',
        'relationship' => '\\rsanchez\\Deep\\Model\\Hydrator\\RelationshipHydrator',
        'assets'       => '\\rsanchez\\Deep\\Model\\Hydrator\\AssetsHydrator',
        'file'         => '\\rsanchez\\Deep\\Model\\Hydrator\\FileHydrator',
        'date'         => '\\rsanchez\\Deep\\Model\\Hydrator\\DateHydrator',
    );

    /**
     * Map of fieldtypes to field IDs:
     *    'fieldtype_name' => array(1, 2, 3),
     * @var array
     */
    protected $fieldtypes = array();
    public $matrixFieldtypes = array();
    public $gridFieldtypes = array();

    /**
     * Playa related entries
     * @var \rsanchez\Deep\Model\Collection\EntryCollection
     */
    protected $playaEntries;

    /**
     * Relationship related entries
     * @var \rsanchez\Deep\Model\Collection\EntryCollection
     */
    protected $relationshipEntries;

    public function hydrate()
    {
        $fieldtypes =& $this->fieldtypes;

        $this->allEntryIds = $entryIds = $this->modelKeys();

        // loop through all the fields used in this collection to gather a list of fieldtypes used
        $this->fetch('channel.fields')->each(function ($rows) use (&$fieldtypes) {
            foreach ($rows as $row) {
                $fieldtypes[$row['field_type']][] = $row['field_id'];
            }
        });

        $hydrators = array();

        foreach (self::$hydrators as $fieldtype => $class) {
            if ($this->hasFieldtype($fieldtype)) {
                $hydrators[$fieldtype] = new $class($this);
            }
        }

        // loop through the hydrators for preloading
        foreach ($hydrators as $hydrator) {
            $hydrator->preload($this);
        }

        // loop again to actually hydrate
        foreach ($hydrators as $hydrator) {
            $hydrator->hydrate($this);
        }

    }

    /**
     * Get all the entry Ids from this collection.
     * This includes both the entries directly in this collection,
     * and entries found in Playa/Relationship fields
     * @return array
     */
    public function allEntryIds()
    {
        return $this->allEntryIds();
    }

    public function hasFieldtype($fieldtype)
    {
        return array_key_exists($fieldtype, $this->fieldtypes) || $this->hasMatrixCol($fieldtype) || $this->hasGridCol($fieldtype);
    }

    public function hasMatrixCol($fieldtype)
    {
        return array_key_exists($fieldtype, $this->matrixFieldtypes);
    }

    public function hasGridCol($fieldtype)
    {
        return array_key_exists($fieldtype, $this->gridFieldtypes);
    }

    public function getFieldIdsByFieldtype($fieldtype)
    {
        return isset($this->fieldtypes[$fieldtype]) ? $this->fieldtypes[$fieldtype] : array();
    }

    /**
     * Set the Matrix columns for this collection
     */
    public function setMatrixCols(Collection $matrixCols)
    {
        $matrixFieldtypes =& $this->matrixFieldtypes;

        $matrixCols->each(function ($col) use ($matrixFieldtypes) {
            $matrixFieldtypes[$col->col_type][] = $col->col_id;
        });

        $this->matrixCols = $matrixCols;
    }

    public function getMatrixCols()
    {
        return $this->matrixCols;
    }

    public function setGridCols(Collection $gridCols)
    {
        $gridFieldtypes =& $this->gridFieldtypes;

        $gridCols->each(function ($col) use (&$gridFieldtypes) {
            $gridFieldtypes[$col->col_type][] = $col->col_id;
        });

        $this->gridCols = $gridCols;
    }

    public function getGridCols()
    {
        return $this->gridCols;
    }

    public function setPlayaEntries(Collection $playaEntries)
    {
        $this->playaEntries = $playaEntries;

        $this->allEntryIds += $playaEntries->modelKeys();
    }

    public function getPlayaEntries()
    {
        return $this->playaEntries;
    }

    public function setRelationshipEntries(Collection $relationshipEntries)
    {
        $this->relationshipEntries = $relationshipEntries;

        $this->allEntryIds += $relationshipEntries->modelKeys();
    }

    public function getRelationshipEntries()
    {
        return $this->relationshipEntries;
    }
}
