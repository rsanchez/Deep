<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Hydrator;

use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Hydrator\DefaultHydrator;
use rsanchez\Deep\Repository\SiteRepository;
use rsanchez\Deep\Repository\UploadPrefRepository;

/**
 * Factory for building new Hydrators
 */
class HydratorFactory
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

    protected $siteRepository;
    protected $uploadPrefRepository;

    public function __construct(SiteRepository $siteRepository, UploadPrefRepository $uploadPrefRepository)
    {
        $this->siteRepository = $siteRepository;
        $this->uploadPrefRepository = $uploadPrefRepository;
    }

    /**
     * Get an array of Hydrators needed by the specified collection
     *    'field_name' => AbstractHydrator
     * @param  \rsanchez\Deep\Collection\EntryCollection $collection
     * @return array                                     AbstractHydrator[]
     */
    public function getHydrators(EntryCollection $collection)
    {
        $hydrators = array();

        // add the built-in ones
        foreach ($this->hydrators as $fieldtype => $class) {
            if ($collection->hasFieldtype($fieldtype)) {
                $hydrators[$fieldtype] = $this->newHydrator($collection, $fieldtype);
            }
        }

        // create default hydrators for fieldtypes not accounted for
        foreach ($collection->getFieldtypes() as $fieldtype) {
            if (! array_key_exists($fieldtype, $hydrators)) {
                $hydrators[$fieldtype] = new DefaultHydrator($collection, $fieldtype);
            }
        }

        return $hydrators;
    }

    /**
     * Create a new Hydrator object
     * @param  \rsanchez\Deep\Collection\EntryCollection $collection
     * @param  string                                    $fieldtype
     * @return \rsanchez\Deep\Hydrator\AbstractHydrator
     */
    public function newHydrator(EntryCollection $collection, $fieldtype)
    {
        $class = $this->hydrators[$fieldtype];

        $baseClass = basename(str_replace('\\', DIRECTORY_SEPARATOR, $class));

        $method = 'new'.$baseClass;

        // some hydrators may have dependencies to be injected
        if (method_exists($this, $method)) {
            return $this->$method($collection, $fieldtype);
        }

        return new $class($collection, $fieldtype);
    }

    /**
     * Create a new Hydrator object
     * @param  \rsanchez\Deep\Collection\EntryCollection $collection
     * @param  string                                    $fieldtype
     * @return \rsanchez\Deep\Hydrator\WysiwygHydrator
     */
    public function newWysiwygHydrator(EntryCollection $collection, $fieldtype, $uploadPrefsRepository, $siteRepository)
    {
    }
}
