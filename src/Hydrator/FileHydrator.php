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
use rsanchez\Deep\Model\Entry;
use rsanchez\Deep\Hydrator\AbstractHydrator;
use rsanchez\Deep\Model\File;
use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Repository\UploadPrefRepository;

/**
 * Hydrator for the File fieldtype
 */
class FileHydrator extends AbstractHydrator
{
    /**
     * All File selections for this collection
     * @var \rsanchez\Deep\Collection\FileCollection
     */
    protected $files;

    /**
     * UploadPref model repository
     * @var \rsanchez\Deep\Repository\UploadPrefRepository
     */
    protected $uploadPrefRepository;

    /**
     * {@inheritdoc}
     *
     * @param \rsanchez\Deep\Collection\EntryCollection    $collection
     * @param string                                       $fieldtype
     * @var \rsanchez\Deep\Repository\UploadPrefRepository $uploadPrefRepository
     */
    public function __construct(EntryCollection $collection, $fieldtype, UploadPrefRepository $uploadPrefRepository)
    {
        parent::__construct($collection, $fieldtype);

        $this->uploadPrefRepository = $uploadPrefRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function preload(array $entryIds)
    {
        $this->files = File::fromEntryCollection($this->collection)->get();

        foreach ($this->files as $file) {
            if ($file->upload_location_id && $uploadPref = $this->uploadPrefRepository->find($file->upload_location_id)) {
                $file->setUploadPref($uploadPref);
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
        $files = $this->files;

        // loop through all file fields
        $entry->channel->fieldsByType($this->fieldtype)->each(function ($field) use ($entry, $files) {

            $entry->setAttribute($field->field_name, $files->filter(function ($file) use ($entry, $field) {
                return $entry->getAttribute('field_id_'.$field->field_id) === '{filedir_'.$file->upload_location_id.'}'.$file->file_name;
            })->first());

        });

        // loop through all matrix fields
        $entry->channel->fieldsByType('matrix')->each(function ($field) use ($collection, $entry, $files, $fieldtype) {

            $entry->getAttribute($field->field_name)->each(function ($row) use ($collection, $entry, $files, $field, $fieldtype) {

                $cols = $collection->getMatrixCols()->filter(function ($col) use ($field, $fieldtype) {
                    return $col->field_id === $field->field_id && $col->col_type === $fieldtype;
                });

                $cols->each(function ($col) use ($entry, $field, $row, $files) {
                    $row->setAttribute($col->col_name, $files->filter(function ($file) use ($entry, $field, $row, $col) {
                        return $row->getAttribute('col_id_'.$col->col_id) === '{filedir_'.$file->upload_location_id.'}'.$file->file_name;
                    })->first());
                });

            });

        });

        // loop through all grid fields
        $entry->channel->fieldsByType('grid')->each(function ($field) use ($collection, $entry, $files, $fieldtype) {

            $entry->getAttribute($field->field_name)->each(function ($row) use ($collection, $entry, $files, $field, $fieldtype) {

                $cols = $collection->getGridCols()->filter(function ($col) use ($field, $fieldtype) {
                    return $col->field_id === $field->field_id && $col->col_type === $fieldtype;
                });

                $cols->each(function ($col) use ($entry, $field, $row, $files) {
                    $row->setAttribute($col->col_name, $files->filter(function ($file) use ($entry, $field, $row, $col) {
                        return $row->getAttribute('col_id_'.$col->col_id) === '{filedir_'.$file->upload_location_id.'}'.$file->file_name;
                    })->first());
                });

            });

        });
    }
}
