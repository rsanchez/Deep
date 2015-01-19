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
use rsanchez\Deep\Collection\FieldCollection;
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
    protected $hydrators = [
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
    ];

    /**
     * Array of fieldtype => dehydrator class name
     * @var array
     */
    protected $dehydrators = [
        'matrix'                => '\\rsanchez\\Deep\\Hydrator\\MatrixDehydrator',
        'grid'                  => '\\rsanchez\\Deep\\Hydrator\\GridDehydrator',
        'playa'                 => '\\rsanchez\\Deep\\Hydrator\\PlayaDehydrator',
        'relationship'          => '\\rsanchez\\Deep\\Hydrator\\RelationshipDehydrator',
        'assets'                => '\\rsanchez\\Deep\\Hydrator\\AssetsDehydrator',
        'file'                  => '\\rsanchez\\Deep\\Hydrator\\FileDehydrator',
        'date'                  => '\\rsanchez\\Deep\\Hydrator\\DateDehydrator',
        'multi_select'          => '\\rsanchez\\Deep\\Hydrator\\PipeDehydrator',
        'checkboxes'            => '\\rsanchez\\Deep\\Hydrator\\PipeDehydrator',
        'fieldpack_checkboxes'  => '\\rsanchez\\Deep\\Hydrator\\ExplodeDehydrator',
        'fieldpack_multiselect' => '\\rsanchez\\Deep\\Hydrator\\ExplodeDehydrator',
        'fieldpack_list'        => '\\rsanchez\\Deep\\Hydrator\\ExplodeDehydrator',
    ];

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
    public function getHydratorsForCollection(AbstractTitleCollection $collection, array $extraHydrators = array())
    {
        $hydrators = new HydratorCollection();

        if ($collection->hasCustomFields()) {
            // add the built-in ones
            foreach ($this->hydrators as $type => $class) {
                if ($collection->hasFieldtype($type)) {
                    $hydrator = $this->newHydrator($hydrators, $type);

                    $hydrator->bootFromCollection($collection);

                    $hydrators->put($type, $hydrator);
                }
            }
        }

        foreach ($extraHydrators as $type) {
            if (isset($this->hydrators[$type])) {
                $hydrator = $this->newHydrator($hydrators, $type);

                $hydrator->bootFromCollection($collection);

                $hydrators->put($type, $hydrator);
            }
        }

        return $hydrators;
    }

    /**
     * Get an array of Dehydrators needed by the specified collection
     *    'field_name' => AbstractDehydrator
     * @param  \rsanchez\Deep\Collection\AbstractTitleCollection $collection
     * @return \rsanchez\Deep\Hydrator\DehydratorCollection
     */
    public function getDehydratorsForCollection(AbstractTitleCollection $collection)
    {
        $dehydrators = new DehydratorCollection();

        if ($collection->hasCustomFields()) {
            // add the built-in ones
            foreach ($this->dehydrators as $type => $class) {
                if ($collection->hasFieldtype($type)) {
                    $dehydrators->put($type, $this->newDehydrator($type, $dehydrators));
                }
            }
        }

        return $dehydrators;
    }

    /**
     * Get an array of Hydrators needed by the specified collection
     *    'field_name' => AbstractHydrator
     * @param  \rsanchez\Deep\Collection\FieldCollection|null $fields
     * @return \rsanchez\Deep\Hydrator\DehydratorCollection
     */
    public function getDehydrators(FieldCollection $properties = null)
    {
        $dehydrators = new DehydratorCollection();

        if ($properties === null) {
            return $dehydrators;
        }

        foreach ($properties as $property) {
            $type = $property->getType();

            if (! isset($dehydrators[$type]) && isset($this->dehydrators[$type])) {
                $dehydrators->put($type, $this->newDehydrator($type, $dehydrators));
            }

            if ($property->hasChildProperties()) {
                foreach ($property->getChildProperties() as $childProperty) {
                    $childType = $childProperty->getType();

                    if (! isset($dehydrators[$childType]) && isset($this->dehydrators[$childType])) {
                        $dehydrators->put($childType, $this->newDehydrator($childType, $dehydrators));
                    }
                }
            }
        }

        return $dehydrators;
    }

    /**
     * Create a new Hydrator object
     *
     * @param  \rsanchez\Deep\Hydrator\HydratorCollection $hydrators
     * @param  string                                     $type
     * @return \rsanchez\Deep\Hydrator\AbstractHydrator
     */
    public function newHydrator(HydratorCollection $hydrators, $type)
    {
        $class = $this->hydrators[$type];

        $baseClass = basename(str_replace('\\', DIRECTORY_SEPARATOR, $class));

        $method = 'new'.$baseClass;

        // some hydrators may have dependencies to be injected
        if (method_exists($this, $method)) {
            return $this->$method($hydrators, $type);
        }

        return new $class($hydrators, $type);
    }

    /**
     * Create a new Hydrator object
     *
     * @param  string                                       $type
     * @param  \rsanchez\Deep\Hydrator\DehydratorCollection $dehydrators
     * @return \rsanchez\Deep\Hydrator\AbstractDehydrator
     */
    public function newDehydrator($type, DehydratorCollection $dehydrators)
    {
        $class = $this->dehydrators[$type];

        return new $class($this->db, $dehydrators);
    }

    /**
     * Create a new AssetsHydrator object
     *
     * @param  \rsanchez\Deep\Hydrator\HydratorCollection $hydrators
     * @param  string                                     $type
     * @return \rsanchez\Deep\Hydrator\AssetsHydrator
     */
    public function newAssetsHydrator(HydratorCollection $hydrators, $type)
    {
        return new AssetsHydrator($hydrators, $type, $this->asset, $this->uploadPrefRepository);
    }

    /**
     * Create a new FileHydrator object
     *
     * @param  \rsanchez\Deep\Hydrator\HydratorCollection $hydrators
     * @param  string                                     $type
     * @return \rsanchez\Deep\Hydrator\FileHydrator
     */
    public function newFileHydrator(HydratorCollection $hydrators, $type)
    {
        return new FileHydrator($hydrators, $type, $this->file, $this->uploadPrefRepository);
    }

    /**
     * Create a new GridHydrator object
     *
     * @param  \rsanchez\Deep\Hydrator\HydratorCollection $hydrators
     * @param  string                                     $type
     * @return \rsanchez\Deep\Hydrator\GridHydrator
     */
    public function newGridHydrator(HydratorCollection $hydrators, $type)
    {
        return new GridHydrator($hydrators, $type, $this->gridCol, $this->gridRow);
    }

    /**
     * Create a new MatrixHydrator object
     *
     * @param  \rsanchez\Deep\Hydrator\HydratorCollection $hydrators
     * @param  string                                     $type
     * @return \rsanchez\Deep\Hydrator\MatrixHydrator
     */
    public function newMatrixHydrator(HydratorCollection $hydrators, $type)
    {
        return new MatrixHydrator($hydrators, $type, $this->matrixCol, $this->matrixRow);
    }

    /**
     * Create a new PlayaHydrator object
     *
     * @param  \rsanchez\Deep\Hydrator\HydratorCollection $hydrators
     * @param  string                                     $type
     * @return \rsanchez\Deep\Hydrator\PlayaHydrator
     */
    public function newPlayaHydrator(HydratorCollection $hydrators, $type)
    {
        return new PlayaHydrator($hydrators, $type, $this->playaEntry);
    }

    /**
     * Create a new RelationshipHydrator object
     *
     * @param  \rsanchez\Deep\Collection\EntryCollection    $collection
     * @param  \rsanchez\Deep\Hydrator\HydratorCollection   $hydrators
     * @param  string                                       $type
     * @return \rsanchez\Deep\Hydrator\RelationshipHydrator
     */
    public function newRelationshipHydrator(HydratorCollection $hydrators, $type)
    {
        return new RelationshipHydrator($hydrators, $type, $this->relationshipEntry);
    }

    /**
     * Create a new ParentsHydrator object
     *
     * @param  \rsanchez\Deep\Hydrator\HydratorCollection $hydrators
     * @param  string                                     $type
     * @return \rsanchez\Deep\Hydrator\ParentsHydrator
     */
    public function newParentsHydrator(HydratorCollection $hydrators, $type)
    {
        return new ParentsHydrator($hydrators, $type, $this->relationshipEntry);
    }

    /**
     * Create a new SiblingsHydrator object
     *
     * @param  \rsanchez\Deep\Hydrator\HydratorCollection $hydrators
     * @param  string                                     $type
     * @return \rsanchez\Deep\Hydrator\SiblingsHydrator
     */
    public function newSiblingsHydrator(HydratorCollection $hydrators, $type)
    {
        return new SiblingsHydrator($hydrators, $type, $this->relationshipEntry);
    }

    /**
     * Create a new WysiwygHydrator object
     *
     * @param  \rsanchez\Deep\Hydrator\HydratorCollection $hydrators
     * @param  string                                     $type
     * @return \rsanchez\Deep\Hydrator\WysiwygHydrator
     */
    public function newWysiwygHydrator(HydratorCollection $hydrators, $type)
    {
        return new WysiwygHydrator($hydrators, $type, $this->siteRepository, $this->uploadPrefRepository);
    }
}
