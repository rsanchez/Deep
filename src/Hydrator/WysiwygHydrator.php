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
use Illuminate\Database\ConnectionInterface;
use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Model\AbstractProperty;
use rsanchez\Deep\Model\AbstractEntity;
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
     * @param \Illuminate\Database\ConnectionInterface                $db
     * @param \rsanchez\Deep\Collection\EntryCollection               $collection
     * @param \rsanchez\Deep\Hydrator\HydratorCollection              $hydrators
     * @param string                                                  $fieldtype
     * @param \rsanchez\Deep\Repository\SiteRepository                $siteRepository
     * @param \rsanchez\Deep\Repository\UploadPrefRepositoryInterface $uploadPrefRepository
     */
    public function __construct(ConnectionInterface $db, EntryCollection $collection, HydratorCollection $hydrators, $fieldtype, SiteRepository $siteRepository, UploadPrefRepositoryInterface $uploadPrefRepository)
    {
        parent::__construct($db, $collection, $hydrators, $fieldtype);

        $this->siteRepository = $siteRepository;

        $this->uploadPrefRepository = $uploadPrefRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(AbstractEntity $entity, AbstractProperty $property)
    {
        $value = $this->parse($entity->getAttribute($property->getIdentifier()));

        $entity->setAttribute($property->getName(), $value);

        return $value;
    }

    /**
     * Parse a string for these values:
     *
     * {filedir_X}, {assets_X:file_name}, {page_X}
     *
     * @param  string $value WYSIWYG content
     * @return string
     */
    public function parse($value)
    {
        if ($value === null || $value === false || $value === '') {
            return '';
        }

        preg_match_all('#{page_(\d+)}#', $value, $pageMatches);

        foreach ($pageMatches[1] as $i => $entryId) {
            if ($pageUri = $this->siteRepository->getPageUri($entryId)) {
                $value = str_replace($pageMatches[0][$i], $pageUri, $value);
            }
        }

        preg_match_all('#{filedir_(\d+)}#', $value, $filedirMatches);

        foreach ($filedirMatches[1] as $i => $id) {
            if ($uploadPref = $this->uploadPrefRepository->find($id)) {
                $value = str_replace($filedirMatches[0][$i], $uploadPref->url, $value);
            }
        }

        // this is all we need to do for now, since we are only supporting Assets locally, not S3 etc.
        preg_match_all('#{assets_\d+:(.*?)}#', $value, $assetsMatches);

        foreach ($assetsMatches[1] as $i => $url) {
            $value = str_replace($assetsMatches[0][$i], $url, $value);
        }

        return $value;
    }
}
