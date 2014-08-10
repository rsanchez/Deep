<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Hydrator;

use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Collection\AbstractTitleCollection;
use rsanchez\Deep\Hydrator\DefaultHydrator;
use rsanchez\Deep\Repository\SiteRepository;
use rsanchez\Deep\Repository\UploadPrefRepositoryInterface;
use rsanchez\Deep\Model\Asset;
use rsanchez\Deep\Model\File;
use rsanchez\Deep\Model\GridCol;
use rsanchez\Deep\Model\GridRow;
use rsanchez\Deep\Model\MatrixCol;
use rsanchez\Deep\Model\MatrixRow;
use rsanchez\Deep\Model\PlayaEntry;
use rsanchez\Deep\Model\RelationshipEntry;

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
        'wygwam'                => '\\rsanchez\\Deep\\Hydrator\\WysiwygHydrator',
        'parents'               => '\\rsanchez\\Deep\\Hydrator\\ParentsHydrator',
        'siblings'              => '\\rsanchez\\Deep\\Hydrator\\SiblingsHydrator',
    );

    /**
     * Site model repository
     * @var \rsanchez\Deep\Repository\SiteRepository
     */
    protected $siteRepository;

    /**
     * UploadPref model repository
     * @var \rsanchez\Deep\Repository\UploadPrefRepository
     */
    protected $uploadPrefRepository;

    /**
     * Constructor
     *
     * @var \rsanchez\Deep\Repository\SiteRepository                $siteRepository
     * @var \rsanchez\Deep\Repository\UploadPrefRepositoryInterface $uploadPrefRepository
     */
    public function __construct(
        SiteRepository $siteRepository,
        UploadPrefRepositoryInterface $uploadPrefRepository,
        Asset $asset,
        File $file,
        GridCol $gridCol,
        GridRow $gridRow,
        MatrixCol $matrixCol,
        MatrixRow $matrixRow,
        PlayaEntry $playaEntry,
        RelationshipEntry $relationshipEntry
    ) {
        $this->siteRepository = $siteRepository;
        $this->uploadPrefRepository = $uploadPrefRepository;
        $this->asset = $asset;
        $this->file =  $file;
        $this->gridCol = $gridCol;
        $this->gridRow = $gridRow;
        $this->matrixCol = $matrixCol;
        $this->matrixRow = $matrixRow;
        $this->playaEntry = $playaEntry;
        $this->relationshipEntry = $relationshipEntry;
    }

    /**
     * Get an array of Hydrators needed by the specified collection
     *    'field_name' => AbstractHydrator
     * @param  \rsanchez\Deep\Collection\AbstractTitleCollection $collection
     * @return array                                             AbstractHydrator[]
     */
    public function getHydrators(AbstractTitleCollection $collection, array $extraHydrators = array())
    {
        $hydrators = array();

        if ($collection->hasCustomFields()) {
            // add the built-in ones
            foreach ($this->hydrators as $type => $class) {
                if ($collection->hasFieldtype($type)) {
                    $hydrators[$type] = $this->newHydrator($collection, $type);
                }
            }
        }

        foreach ($extraHydrators as $type) {
            if (isset($this->hydrators[$type])) {
                $hydrators[$type] = $this->newHydrator($collection, $type);
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
     * Create a new AssetsHydrator object
     * @param  \rsanchez\Deep\Collection\EntryCollection $collection
     * @param  string                                    $fieldtype
     * @return \rsanchez\Deep\Hydrator\AssetsHydrator
     */
    public function newAssetsHydrator(EntryCollection $collection, $fieldtype)
    {
        return new AssetsHydrator($collection, $fieldtype, $this->asset, $this->uploadPrefRepository);
    }

    /**
     * Create a new FileHydrator object
     * @param  \rsanchez\Deep\Collection\EntryCollection $collection
     * @param  string                                    $fieldtype
     * @return \rsanchez\Deep\Hydrator\FileHydrator
     */
    public function newFileHydrator(EntryCollection $collection, $fieldtype)
    {
        return new FileHydrator($collection, $fieldtype, $this->file, $this->uploadPrefRepository);
    }

    /**
     * Create a new GridHydrator object
     * @param  \rsanchez\Deep\Collection\EntryCollection $collection
     * @param  string                                    $fieldtype
     * @return \rsanchez\Deep\Hydrator\GridHydrator
     */
    public function newGridHydrator(EntryCollection $collection, $fieldtype)
    {
        return new GridHydrator($collection, $fieldtype, $this->gridCol, $this->gridRow);
    }

    /**
     * Create a new MatrixHydrator object
     * @param  \rsanchez\Deep\Collection\EntryCollection $collection
     * @param  string                                    $fieldtype
     * @return \rsanchez\Deep\Hydrator\MatrixHydrator
     */
    public function newMatrixHydrator(EntryCollection $collection, $fieldtype)
    {
        return new MatrixHydrator($collection, $fieldtype, $this->matrixCol, $this->matrixRow);
    }

    /**
     * Create a new PlayaHydrator object
     * @param  \rsanchez\Deep\Collection\EntryCollection $collection
     * @param  string                                    $fieldtype
     * @return \rsanchez\Deep\Hydrator\PlayaHydrator
     */
    public function newPlayaHydrator(EntryCollection $collection, $fieldtype)
    {
        return new PlayaHydrator($collection, $fieldtype, $this->playaEntry);
    }

    /**
     * Create a new RelationshipHydrator object
     * @param  \rsanchez\Deep\Collection\EntryCollection    $collection
     * @param  string                                       $fieldtype
     * @return \rsanchez\Deep\Hydrator\RelationshipHydrator
     */
    public function newRelationshipHydrator(EntryCollection $collection, $fieldtype)
    {
        return new RelationshipHydrator($collection, $fieldtype, $this->relationshipEntry);
    }

    /**
     * Create a new ParentsHydrator object
     * @param  \rsanchez\Deep\Collection\EntryCollection    $collection
     * @param  string                                       $fieldtype
     * @return \rsanchez\Deep\Hydrator\ParentsHydrator
     */
    public function newParentsHydrator(EntryCollection $collection, $fieldtype)
    {
        return new ParentsHydrator($collection, $fieldtype, $this->relationshipEntry);
    }

    /**
     * Create a new SIblingsHydrator object
     * @param  \rsanchez\Deep\Collection\EntryCollection    $collection
     * @param  string                                       $fieldtype
     * @return \rsanchez\Deep\Hydrator\SiblingsHydrator
     */
    public function newSiblingsHydrator(EntryCollection $collection, $fieldtype)
    {
        return new SiblingsHydrator($collection, $fieldtype, $this->relationshipEntry);
    }

    /**
     * Create a new WysiwygHydrator object
     * @param  \rsanchez\Deep\Collection\EntryCollection $collection
     * @param  string                                    $fieldtype
     * @return \rsanchez\Deep\Hydrator\WysiwygHydrator
     */
    public function newWysiwygHydrator(EntryCollection $collection, $fieldtype)
    {
        return new WysiwygHydrator($collection, $fieldtype, $this->siteRepository, $this->uploadPrefRepository);
    }
}
