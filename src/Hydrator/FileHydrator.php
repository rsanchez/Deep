<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Hydrator;

use rsanchez\Deep\Model\PropertyInterface;
use rsanchez\Deep\Model\AbstractEntity;
use rsanchez\Deep\Model\File;
use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Repository\UploadPrefRepositoryInterface;

/**
 * Hydrator for the File fieldtype
 */
class FileHydrator extends AbstractHydrator
{
    /**
     * @var \rsanchez\Deep\Model\File
     */
    protected $model;

    /**
     * File selections sorted by name
     * @var array
     */
    protected $files;

    /**
     * UploadPref model repository
     * @var \rsanchez\Deep\Repository\UploadPrefRepositoryInterface
     */
    protected $uploadPrefRepository;

    /**
     * {@inheritdoc}
     *
     * @param \rsanchez\Deep\Collection\EntryCollection               $collection
     * @param \rsanchez\Deep\Hydrator\HydratorCollection              $hydrators
     * @param string                                                  $fieldtype
     * @param \rsanchez\Deep\Repository\UploadPrefRepositoryInterface $uploadPrefRepository
     */
    public function __construct(EntryCollection $collection, HydratorCollection $hydrators, $fieldtype, File $model, UploadPrefRepositoryInterface $uploadPrefRepository)
    {
        parent::__construct($collection, $hydrators, $fieldtype);

        $this->model = $model;

        $this->uploadPrefRepository = $uploadPrefRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function preload(array $entryIds)
    {
        $query = $this->model->fromEntryCollection($this->collection);

        if (isset($this->hydrators['matrix'])) {
            $query->fromMatrix(
                $this->hydrators['matrix']->getCols(),
                $this->hydrators['matrix']->getRows()
            );
        }

        if (isset($this->hydrators['grid'])) {
            $query->fromGrid(
                $this->hydrators['grid']->getCols(),
                $this->hydrators['grid']->getRows()
            );
        }

        $files = $query->get();

        foreach ($files as $file) {
            if (! $file->upload_location_id || ! $uploadPref = $this->uploadPrefRepository->find($file->upload_location_id)) {
                continue;
            }

            $file->setUploadPref($uploadPref);

            $this->files['{filedir_'.$file->upload_location_id.'}'.$file->file_name] = $file;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(AbstractEntity $entity, PropertyInterface $property)
    {
        $value = $entity->{$property->getIdentifier()};

        return $value && isset($this->files[$value]) ? $this->files[$value] : null;
    }
}
