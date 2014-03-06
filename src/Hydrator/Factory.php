<?php

namespace rsanchez\Deep\Hydrator;

use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Hydrator\DefaultHydrator;

class Factory
{
    /**
     * Array of fieldtype => hydrator class name
     * @var array
     */
    protected $hydrators = array(
        'matrix'                => '\\rsanchez\\Deep\\Hydrator\\MatrixHydrator',
        'grid'                  => '\\rsanchez\\Deep\\Hydrator\\GridHydrator',
        'playa'                 => '\\rsanchez\\Deep\\Hydrator\\PlayaHydrator',
        'relationship'          => '\\rsanchez\\Deep\\Hydrator\\RelationshipHydrator',
        'assets'                => '\\rsanchez\\Deep\\Hydrator\\AssetsHydrator',
        'file'                  => '\\rsanchez\\Deep\\Hydrator\\FileHydrator',
        'date'                  => '\\rsanchez\\Deep\\Hydrator\\DateHydrator',
        'multi_select'          => '\\rsanchez\\Deep\\Hydrator\\PipeHydrator',
        'checkboxes'            => '\\rsanchez\\Deep\\Hydrator\\PipeHydrator',
        'fieldpack_checkboxes'  => '\\rsanchez\\Deep\\Hydrator\\ExplodeHydrator',
        'fieldpack_multiselect' => '\\rsanchez\\Deep\\Hydrator\\ExplodeHydrator',
        'fieldpack_list'        => '\\rsanchez\\Deep\\Hydrator\\ExplodeHydrator',
    );

    public function getHydrators(EntryCollection $collection)
    {
        $hydrators = array();

        foreach ($this->hydrators as $fieldtype => $class) {
            if ($collection->hasFieldtype($fieldtype)) {
                $hydrators[$fieldtype] = $this->newHydrator($collection, $fieldtype);
            }
        }
        return $hydrators;
    }

    public function newHydrator(EntryCollection $collection, $fieldtype)
    {
        if (! array_key_exists($fieldtype, $this->hydrators)) {
            throw new \Exception('Invalid hydrator: '.$fieldtype);
        }

        $method = 'new'.ucfirst($fieldtype).'Hydrator';

        if (method_exists($this, $method)) {
            return $this->$method($collection, $fieldtype);
        }

        $class = $this->hydrators[$fieldtype];

        return new $class($collection, $fieldtype);
    }

    public function newDefaultHydrator(EntryCollection $collection, $fieldtype)
    {
        return new DefaultHydrator($collection, $fieldtype);
    }
}
