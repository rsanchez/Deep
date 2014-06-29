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
use rsanchez\Deep\Model\AbstractProperty;
use rsanchez\Deep\Model\AbstractEntity;
use rsanchez\Deep\Hydrator\AbstractHydrator;
use rsanchez\Deep\Model\File;
use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Repository\UploadPrefRepositoryInterface;

/**
 * Hydrator for the File fieldtype
 */
class FileHydrator extends AbstractHydrator
{
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
        $files = File::fromEntryCollection($this->collection)->get();

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
    public function hydrate(AbstractEntity $entity, AbstractProperty $property)
    {
        $value = $entity->getAttribute($property->getIdentifier());

        $value = $value && isset($this->files[$value]) ? $this->files[$value] : null;

        $entity->setAttribute($property->getName(), $value);

        return $value;
    }
}
