<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Hydrator;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Model\Entry;
use rsanchez\Deep\Hydrator\AbstractHydrator;
use rsanchez\Deep\Model\Asset;
use rsanchez\Deep\Repository\UploadPrefRepositoryInterface;

/**
 * Hydrator for the Assets fieldtype
 */
class AssetsHydrator extends AbstractHydrator
{
    /**
     * All Asset selections for this collection
     * @var \rsanchez\Deep\Collection\AssetCollection
     */
    protected $selections;

    /**
     * UploadPref model repository
     * @var \rsanchez\Deep\Repository\UploadPrefRepositoryInterface
     */
    protected $uploadPrefRepository;

    /**
     * {@inheritdoc}
     *
     * @param \rsanchez\Deep\Collection\EntryCollection $collection
     * @param string                                    $fieldtype
     * @var \rsanchez\Deep\Repository\UploadPrefRepositoryInterface $uploadPrefRepository
     */
    public function __construct(EntryCollection $collection, $fieldtype, UploadPrefRepositoryInterface $uploadPrefRepository)
    {
        parent::__construct($collection, $fieldtype);

        $this->uploadPrefRepository = $uploadPrefRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function preload(array $entryIds)
    {
        $this->selections = Asset::entryId($entryIds)->get();

        foreach ($this->selections as $asset) {
            if ($asset->filedir_id && $uploadPref = $this->uploadPrefRepository->find($asset->filedir_id)) {
                $asset->setUploadPref($uploadPref);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(Entry $entry)
    {
        $fieldtype = $this->fieldtype;
        $collection = $this->collection;
        $selections = $this->selections;

        // loop through all assets fields
        $entry->channel->fieldsByType($this->fieldtype)->each(function ($field) use ($entry, $selections) {

            $entry->setAttribute($field->field_name, $selections->filter(function ($selection) use ($entry, $field) {
                return $entry->getKey() === $selection->getKey() && $field->field_id === $selection->field_id;
            }));

        });

        // loop through all matrix fields
        $entry->channel->fieldsByType('matrix')->each(function ($field) use ($collection, $entry, $selections, $fieldtype) {

            $entry->getAttribute($field->field_name)->each(function ($row) use ($collection, $entry, $selections, $field, $fieldtype) {

                $cols = $collection->getMatrixCols()->filter(function ($col) use ($field, $fieldtype) {
                    return $col->field_id === $field->field_id && $col->col_type === $fieldtype;
                });

                $cols->each(function ($col) use ($entry, $field, $row, $selections) {
                    $row->setAttribute($col->col_name, $selections->filter(function ($selection) use ($entry, $field, $row, $col) {
                        return $entry->getKey() === $selection->getKey() && $col->col_id === $selection->col_id && $selection->content_type === 'matrix';
                    }));
                });

            });

        });

        // loop through all grid fields
        $entry->channel->fieldsByType('grid')->each(function ($field) use ($collection, $entry, $selections, $fieldtype) {

            $entry->getAttribute($field->field_name)->each(function ($row) use ($collection, $entry, $selections, $field, $fieldtype) {

                $cols = $collection->getGridCols()->filter(function ($col) use ($field, $fieldtype) {
                    return $col->field_id === $field->field_id && $col->col_type === $fieldtype;
                });

                $cols->each(function ($col) use ($entry, $field, $row, $selections) {
                    $value = $selections->filter(function ($selection) use ($entry, $field, $row, $col) {
                        return $entry->getKey() === $selection->getKey() && $col->col_id === $selection->col_id;
                    });
                    $row->setAttribute($col->col_name, $value);
                });

            });

        });
    }
}
