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
     * @param \rsanchez\Deep\Hydrator\HydratorCollection              $hydrators
     * @param string                                                  $fieldtype
     * @param \rsanchez\Deep\Repository\UploadPrefRepositoryInterface $uploadPrefRepository
     */
    public function __construct(HydratorCollection $hydrators, $fieldtype, File $model, UploadPrefRepositoryInterface $uploadPrefRepository)
    {
        parent::__construct($hydrators, $fieldtype);

        $this->model = $model;

        $this->uploadPrefRepository = $uploadPrefRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function preload(EntryCollection $collection)
    {
        $query = $this->model->fromEntryCollection($collection);

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
        $entity->addCustomFieldSetter($property->getName(), [$this, 'setter']);

        $value = $entity->{$property->getIdentifier()};

        return $value && isset($this->files[$value]) ? $this->files[$value] : null;
    }

    /**
     * Setter callback
     * @param  \rsanchez\Deep\Model\File|string|int|null $value
     * @return \rsanchez\Deep\Model\File|null
     */
    public function setter($value = null, PropertyInterface $property = null)
    {
        if (is_null($value)) {
            return null;
        }

        if ($value instanceof File) {
            return $value;
        }

        if (is_int($value)) {
            return File::find($value);
        }

        if (is_string($value)) {
            // is it an ID?
            if (preg_match('/^\d+$/', $value)) {
                return File::find($value);
            }

            if (preg_match('/^{filedir_(\d+)}(.*?)$/', $value, $match)) {
                return File::uploadPrefId($match[1])
                    ->fileName($match[2])
                    ->first();
            }
        }

        throw new \InvalidArgumentException('$value must be of type string, int, null, or \rsanchez\Deep\Model\File.');
    }
}
