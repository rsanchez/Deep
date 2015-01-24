<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Hydrator;

use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Model\PropertyInterface;
use rsanchez\Deep\Model\AbstractEntity;
use rsanchez\Deep\Model\Wysiwyg;
use rsanchez\Deep\Repository\SiteRepository;
use rsanchez\Deep\Repository\UploadPrefRepositoryInterface;

/**
 * Hydrator for the WYSIWYG fields
 */
class WysiwygHydrator extends AbstractHydrator
{
    /**
     * UploadPref model repository
     * @var \rsanchez\Deep\Repository\SiteRepository
     */
    protected $siteRepository;

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
     * @param \rsanchez\Deep\Repository\SiteRepository                $siteRepository
     * @param \rsanchez\Deep\Repository\UploadPrefRepositoryInterface $uploadPrefRepository
     */
    public function __construct(HydratorCollection $hydrators, $fieldtype, SiteRepository $siteRepository, UploadPrefRepositoryInterface $uploadPrefRepository)
    {
        parent::__construct($hydrators, $fieldtype);

        $this->siteRepository = $siteRepository;

        $this->uploadPrefRepository = $uploadPrefRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(AbstractEntity $entity, PropertyInterface $property)
    {
        return new Wysiwyg($this->siteRepository, $this->uploadPrefRepository, $entity->{$property->getIdentifier()});
    }
}
