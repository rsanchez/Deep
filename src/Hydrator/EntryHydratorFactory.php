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
 * Factory for building new Hydrators for Entries
 */
class EntryHydratorFactory extends AbstractHydratorFactory
{
    /**
     * @var \rsanchez\Deep\Model\GridCol
     */
    protected $gridCol;

    /**
     * @var \rsanchez\Deep\Model\GridRow
     */
    protected $gridRow;

    /**
     * @var \rsanchez\Deep\Model\MatrixCol
     */
    protected $matrixCol;

    /**
     * @var \rsanchez\Deep\Model\MatrixRow
     */
    protected $matrixRow;

    /**
     * Constructor
     * @param \Illuminate\Database\ConnectionInterface                $db
     * @param \rsanchez\Deep\Repository\SiteRepository                $siteRepository
     * @param \rsanchez\Deep\Repository\UploadPrefRepositoryInterface $uploadPrefRepository
     * @param \rsanchez\Deep\Model\Asset                              $asset
     * @param \rsanchez\Deep\Model\File                               $file
     * @param \rsanchez\Deep\Model\PlayaEntry                         $playaEntry
     * @param \rsanchez\Deep\Model\RelationshipEntry                  $relationshipEntry
     * @param \rsanchez\Deep\Model\GridCol                            $gridCol
     * @param \rsanchez\Deep\Model\GridRow                            $gridRow
     * @param \rsanchez\Deep\Model\MatrixCol                          $matrixCol
     * @param \rsanchez\Deep\Model\MatrixRow                          $matrixRow
     */
    public function __construct(
        ConnectionInterface $db,
        SiteRepository $siteRepository,
        UploadPrefRepositoryInterface $uploadPrefRepository,
        Asset $asset,
        File $file,
        PlayaEntry $playaEntry,
        RelationshipEntry $relationshipEntry,
        GridCol $gridCol,
        GridRow $gridRow,
        MatrixCol $matrixCol,
        MatrixRow $matrixRow
    ) {
        parent::__construct(
            $db,
            $siteRepository,
            $uploadPrefRepository,
            $asset, $file,
            $playaEntry,
            $relationshipEntry
        );

        $this->gridCol = $gridCol;
        $this->gridRow = $gridRow;
        $this->matrixCol = $matrixCol;
        $this->matrixRow = $matrixRow;
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
}
