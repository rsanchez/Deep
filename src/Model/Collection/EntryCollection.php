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

    protected $entryIds = array();
    protected $fieldtypes = array();

    /**
     * Map of fieldtypes to field IDs:
     *    'fieldtype_name' => array(1, 2, 3),
     * @var array
     */
    protected $fieldIdsByFieldtype = array();

    public function hydrate()
    {
        $fieldtypes =& $this->fieldtypes;
        $fieldIdsByFieldtype =& $this->fieldIdsByFieldtype;

        $entryIds = $this->modelKeys();

        $this->entryIds = $entryIds;

        // loop through all the fields used in this collection to gather a list of fieldtypes used
        $this->fetch('channel.fields')->each(function ($rows) use (&$fieldtypes, &$fieldIdsByFieldtype) {
            foreach ($rows as $row) {
                $fieldtypes[] = $row['field_type'];
                $fieldIdsByFieldtype[$row['field_type']][] = $row['field_id'];
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
        foreach ($this as $entry) {
            foreach ($hydrators as $hydrator) {
                $hydrator->hydrate($this, $entry);
            }
        }
    }

    /**
     * Get all the entry Ids from this collection.
     * This includes both the entries directly in this collection,
     * and entries found in Playa/Relationship fields
     * @return array
     */
    public function entryIds()
    {
        return $this->entryIds;
    }

    public function addEntryIds(array $entryIds)
    {
        $this->entryIds = array_unique(array_merge($this->entryIds, $entryIds));
    }

    public function hasFieldtype($fieldtype)
    {
        return in_array($fieldtype, $this->fieldtypes);
    }

    public function getFieldIdsByFieldtype($fieldtype)
    {
        return isset($this->fieldIdsByFieldtype[$fieldtype]) ? $this->fieldIdsByFieldtype[$fieldtype] : array();
    }

    /**
     * Set the Matrix columns for this collection
     */
    public function setMatrixCols(Collection $matrixCols)
    {
        $fieldtypes =& $this->fieldtypes;

        $matrixCols->each(function ($col) use (&$fieldtypes) {
            $fieldtypes[] = $col->col_type;
        });

        $this->matrixCols = $matrixCols;
    }

    public function getMatrixCols()
    {
        return $this->matrixCols;
    }

    public function setGridCols(Collection $gridCols)
    {
        $fieldtypes =& $this->fieldtypes;

        $gridCols->each(function ($col) use (&$fieldtypes) {
            $fieldtypes[] = $col->col_type;
        });

        $this->gridCols = $gridCols;
    }

    public function getGridCols()
    {
        return $this->gridCols;
    }
}
