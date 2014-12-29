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
use Illuminate\Database\ConnectionInterface;

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
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $db;

    /**
     * Constructor
     *
     * @var \rsanchez\Deep\Repository\SiteRepository                $siteRepository
     * @var \rsanchez\Deep\Repository\UploadPrefRepositoryInterface $uploadPrefRepository
     */
    public function __construct(
        ConnectionInterface $db,
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
        $this->db = $db;
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
        $hydrators = new HydratorCollection();

        if ($collection->hasCustomFields()) {
            // add the built-in ones
            foreach ($this->hydrators as $type => $class) {
                if ($collection->hasFieldtype($type)) {
                    $hydrators->put($type, $this->newHydrator($collection, $hydrators, $type));
                }
            }
        }

        foreach ($extraHydrators as $type) {
            if (isset($this->hydrators[$type])) {
                $hydrators->put($type, $this->newHydrator($collection, $hydrators, $type));
            }
        }

        return $hydrators;
    }

    /**
     * Create a new Hydrator object
     *
     * @param  \rsanchez\Deep\Collection\EntryCollection  $collection
     * @param  \rsanchez\Deep\Hydrator\HydratorCollection $hydrators
     * @param  string                                     $fieldtype
     * @return \rsanchez\Deep\Hydrator\AbstractHydrator
     */
    public function newHydrator(EntryCollection $collection, HydratorCollection $hydrators, $fieldtype)
    {
        $class = $this->hydrators[$fieldtype];

        $baseClass = basename(str_replace('\\', DIRECTORY_SEPARATOR, $class));

        $method = 'new'.$baseClass;

        // some hydrators may have dependencies to be injected
        if (method_exists($this, $method)) {
            return $this->$method($collection, $hydrators, $fieldtype);
        }

        return new $class($this->db, $collection, $hydrators, $fieldtype);
    }

    /**
     * Create a new AssetsHydrator object
     *
     * @param  \rsanchez\Deep\Collection\EntryCollection  $collection
     * @param  \rsanchez\Deep\Hydrator\HydratorCollection $hydrators
     * @param  string                                     $fieldtype
     * @return \rsanchez\Deep\Hydrator\AssetsHydrator
     */
    public function newAssetsHydrator(EntryCollection $collection, HydratorCollection $hydrators, $fieldtype)
    {
        return new AssetsHydrator($this->db, $collection, $hydrators, $fieldtype, $this->asset, $this->uploadPrefRepository);
    }

    /**
     * Create a new FileHydrator object
     *
     * @param  \rsanchez\Deep\Collection\EntryCollection  $collection
     * @param  \rsanchez\Deep\Hydrator\HydratorCollection $hydrators
     * @param  string                                     $fieldtype
     * @return \rsanchez\Deep\Hydrator\FileHydrator
     */
    public function newFileHydrator(EntryCollection $collection, HydratorCollection $hydrators, $fieldtype)
    {
        return new FileHydrator($this->db, $collection, $hydrators, $fieldtype, $this->file, $this->uploadPrefRepository);
    }

    /**
     * Create a new GridHydrator object
     *
     * @param  \rsanchez\Deep\Collection\EntryCollection  $collection
     * @param  \rsanchez\Deep\Hydrator\HydratorCollection $hydrators
     * @param  string                                     $fieldtype
     * @return \rsanchez\Deep\Hydrator\GridHydrator
     */
    public function newGridHydrator(EntryCollection $collection, HydratorCollection $hydrators, $fieldtype)
    {
        return new GridHydrator($this->db, $collection, $hydrators, $fieldtype, $this->gridCol, $this->gridRow);
    }

    /**
     * Create a new MatrixHydrator object
     *
     * @param  \rsanchez\Deep\Collection\EntryCollection  $collection
     * @param  \rsanchez\Deep\Hydrator\HydratorCollection $hydrators
     * @param  string                                     $fieldtype
     * @return \rsanchez\Deep\Hydrator\MatrixHydrator
     */
    public function newMatrixHydrator(EntryCollection $collection, HydratorCollection $hydrators, $fieldtype)
    {
        return new MatrixHydrator($this->db, $collection, $hydrators, $fieldtype, $this->matrixCol, $this->matrixRow);
    }

    /**
     * Create a new PlayaHydrator object
     *
     * @param  \rsanchez\Deep\Collection\EntryCollection  $collection
     * @param  \rsanchez\Deep\Hydrator\HydratorCollection $hydrators
     * @param  string                                     $fieldtype
     * @return \rsanchez\Deep\Hydrator\PlayaHydrator
     */
    public function newPlayaHydrator(EntryCollection $collection, HydratorCollection $hydrators, $fieldtype)
    {
        return new PlayaHydrator($this->db, $collection, $hydrators, $fieldtype, $this->playaEntry);
    }

    /**
     * Create a new RelationshipHydrator object
     *
     * @param  \rsanchez\Deep\Collection\EntryCollection    $collection
     * @param  \rsanchez\Deep\Hydrator\HydratorCollection   $hydrators
     * @param  string                                       $fieldtype
     * @return \rsanchez\Deep\Hydrator\RelationshipHydrator
     */
    public function newRelationshipHydrator(EntryCollection $collection, HydratorCollection $hydrators, $fieldtype)
    {
        return new RelationshipHydrator($this->db, $collection, $hydrators, $fieldtype, $this->relationshipEntry);
    }

    /**
     * Create a new ParentsHydrator object
     *
     * @param  \rsanchez\Deep\Collection\EntryCollection  $collection
     * @param  \rsanchez\Deep\Hydrator\HydratorCollection $hydrators
     * @param  string                                     $fieldtype
     * @return \rsanchez\Deep\Hydrator\ParentsHydrator
     */
    public function newParentsHydrator(EntryCollection $collection, HydratorCollection $hydrators, $fieldtype)
    {
        return new ParentsHydrator($this->db, $collection, $hydrators, $fieldtype, $this->relationshipEntry);
    }

    /**
     * Create a new SiblingsHydrator object
     *
     * @param  \rsanchez\Deep\Collection\EntryCollection  $collection
     * @param  \rsanchez\Deep\Hydrator\HydratorCollection $hydrators
     * @param  string                                     $fieldtype
     * @return \rsanchez\Deep\Hydrator\SiblingsHydrator
     */
    public function newSiblingsHydrator(EntryCollection $collection, HydratorCollection $hydrators, $fieldtype)
    {
        return new SiblingsHydrator($this->db, $collection, $hydrators, $fieldtype, $this->relationshipEntry);
    }

    /**
     * Create a new WysiwygHydrator object
     *
     * @param  \rsanchez\Deep\Collection\EntryCollection  $collection
     * @param  \rsanchez\Deep\Hydrator\HydratorCollection $hydrators
     * @param  string                                     $fieldtype
     * @return \rsanchez\Deep\Hydrator\WysiwygHydrator
     */
    public function newWysiwygHydrator(EntryCollection $collection, HydratorCollection $hydrators, $fieldtype)
    {
        return new WysiwygHydrator($this->db, $collection, $hydrators, $fieldtype, $this->siteRepository, $this->uploadPrefRepository);
    }
}
