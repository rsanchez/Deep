<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Hydrator;

use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Collection\FieldCollection;
use rsanchez\Deep\Collection\PropertyCollection;
use rsanchez\Deep\Repository\SiteRepositoryInterface;
use rsanchez\Deep\Repository\UploadPrefRepositoryInterface;
use rsanchez\Deep\Model\Asset;
use rsanchez\Deep\Model\File;
use rsanchez\Deep\Model\MatrixCol;
use rsanchez\Deep\Model\PlayaEntry;
use rsanchez\Deep\Model\RelationshipEntry;
use Illuminate\Database\ConnectionInterface;

/**
 * Factory for building new Hydrators
 */
abstract class AbstractHydratorFactory
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
     * @var \rsanchez\Deep\Repository\SiteRepositoryInterface
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
     * @var \rsanchez\Deep\Model\Asset
     */
    protected $asset;

    /**
     * @var \rsanchez\Deep\Model\File
     */
    protected $file;

    /**
     * @var \rsanchez\Deep\Model\PlayaEntry
     */
    protected $playaEntry;

    /**
     * @var \rsanchez\Deep\Model\RelationshipEntry
     */
    protected $relationshipEntry;

    /**
     * Constructor
     * @param \Illuminate\Database\ConnectionInterface                $db
     * @param \rsanchez\Deep\Repository\SiteRepositoryInterface       $siteRepository
     * @param \rsanchez\Deep\Repository\UploadPrefRepositoryInterface $uploadPrefRepository
     * @param \rsanchez\Deep\Model\Asset                              $asset
     * @param \rsanchez\Deep\Model\File                               $file
     * @param \rsanchez\Deep\Model\PlayaEntry                         $playaEntry
     * @param \rsanchez\Deep\Model\RelationshipEntry                  $relationshipEntry
     */
    public function __construct(
        ConnectionInterface $db,
        SiteRepositoryInterface $siteRepository,
        UploadPrefRepositoryInterface $uploadPrefRepository,
        Asset $asset,
        File $file,
        PlayaEntry $playaEntry,
        RelationshipEntry $relationshipEntry
    ) {
        $this->db = $db;
        $this->siteRepository = $siteRepository;
        $this->uploadPrefRepository = $uploadPrefRepository;
        $this->asset = $asset;
        $this->file =  $file;
        $this->playaEntry = $playaEntry;
        $this->relationshipEntry = $relationshipEntry;
    }

    /**
     * Get an array of Hydrators needed by the specified collection
     *    'field_name' => AbstractHydrator
     * @param  \rsanchez\Deep\Collection\EntryCollection $collection
     * @return array                                             AbstractHydrator[]
     */
    public function getHydratorsForCollection(EntryCollection $collection, $childHydrationEnabled = true, array $extraHydrators = [])
    {
        $hydrators = new HydratorCollection();

        if ($collection->hasCustomFields()) {
            // add the built-in ones
            foreach ($this->hydrators as $type => $class) {
                if ($collection->hasFieldtype($type)) {
                    $hydrator = $this->newHydrator($hydrators, $type, $childHydrationEnabled);

                    $hydrator->bootFromCollection($collection);

                    $hydrators->put($type, $hydrator);
                }
            }
        }

        foreach ($extraHydrators as $type) {
            if (isset($this->hydrators[$type])) {
                $hydrator = $this->newHydrator($hydrators, $type, $childHydrationEnabled);

                $hydrator->bootFromCollection($collection);

                $hydrators->put($type, $hydrator);
            }
        }

        return $hydrators;
    }

    /**
     * Get an array of Hydrators needed by the specified collection
     *    'field_name' => AbstractHydrator
     * @param  \rsanchez\Deep\Collection\PropertyCollection|null $fields
     * @return \rsanchez\Deep\Hydrator\DehydratorCollection
     */
    public function getHydrators(PropertyCollection $properties = null)
    {
        $hydrators = new HydratorCollection();

        if ($properties === null) {
            return $hydrators;
        }

        foreach ($properties as $property) {
            $type = $property->getType();

            if (! isset($hydrators[$type]) && isset($this->hydrators[$type])) {
                $hydrators->put($type, $this->newHydrator($hydrators, $type));
            }

            if ($property->hasChildProperties()) {
                foreach ($property->getChildProperties() as $childProperty) {
                    $childType = $childProperty->getType();

                    if (! isset($hydrators[$childType]) && isset($this->hydrators[$childType])) {
                        $hydrators->put($childType, $this->newHydrator($hydrators, $childType));
                    }
                }
            }
        }

        return $hydrators;
    }

    /**
     * Get an array of Dehydrators needed by the specified collection
     *    'field_name' => AbstractDehydrator
     * @param  \rsanchez\Deep\Collection\EntryCollection $collection
     * @return \rsanchez\Deep\Hydrator\DehydratorCollection
     */
    public function getDehydratorsForCollection(EntryCollection $collection)
    {
        $dehydrators = new DehydratorCollection();

        if ($collection->hasCustomFields()) {
            // add the built-in ones
            foreach ($this->dehydrators as $type => $class) {
                if ($collection->hasFieldtype($type)) {
                    $dehydrators->put($type, $this->newDehydrator($dehydrators, $type));
                }
            }
        }

        return $dehydrators;
    }

    /**
     * Get an array of Hydrators needed by the specified collection
     *    'field_name' => AbstractHydrator
     * @param  \rsanchez\Deep\Collection\PropertyCollection|null $properties
     * @return \rsanchez\Deep\Hydrator\DehydratorCollection
     */
    public function getDehydrators(PropertyCollection $properties = null)
    {
        $dehydrators = new DehydratorCollection();

        if ($properties === null) {
            return $dehydrators;
        }

        foreach ($properties as $property) {
            $type = $property->getType();

            if (! isset($dehydrators[$type]) && isset($this->dehydrators[$type])) {
                $dehydrators->put($type, $this->newDehydrator($dehydrators, $type));
            }

            if ($property->hasChildProperties()) {
                foreach ($property->getChildProperties() as $childProperty) {
                    $childType = $childProperty->getType();

                    if (! isset($dehydrators[$childType]) && isset($this->dehydrators[$childType])) {
                        $dehydrators->put($childType, $this->newDehydrator($dehydrators, $childType));
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
    public function newHydrator(HydratorCollection $hydrators, $type, $childHydrationEnabled = true)
    {
        $class = $this->hydrators[$type];

        $baseClass = basename(str_replace('\\', DIRECTORY_SEPARATOR, $class));

        $method = 'new'.$baseClass;

        // some hydrators may have dependencies to be injected
        if (method_exists($this, $method)) {
            $hydrator = $this->$method($hydrators, $type);
        } else {
            $hydrator = new $class($hydrators, $type);
        }

        if (!$childHydrationEnabled) {
            $hydrator->setChildHydrationDisabled();
        }

        return $hydrator;
    }

    /**
     * Create a new Hydrator object
     *
     * @param  \rsanchez\Deep\Hydrator\DehydratorCollection $dehydrators
     * @param  string                                       $type
     * @return \rsanchez\Deep\Hydrator\AbstractDehydrator
     */
    public function newDehydrator(DehydratorCollection $dehydrators, $type)
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
