<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use rsanchez\Deep\Repository\UploadPrefRepositoryInterface;
use rsanchez\Deep\Repository\SiteRepositoryInterface;

class Wysiwyg implements StringableInterface
{
    /**
     * The original unparsed text
     * @var string
     */
    protected $originalRawValue;

    /**
     * The original text, with {page} {filedir} and {assets} tags parsed
     * @var string
     */
    protected $originalParsedValue;

    /**
     * Updated text
     * @var string
     */
    protected $newParsedValue;

    /**
     * Updated text, reverse-parsed from newParsedValue
     * @var string
     */
    protected $newRawValue;

    /**
     * List of asset ID => asset URLs
     * @var array
     */
    protected $assets = [];

    /**
     * Constructor
     *
     * @param \rsanchez\Deep\Repository\SiteRepositoryInterface       $siteRepository
     * @param \rsanchez\Deep\Repository\UploadPrefRepositoryInterface $uploadPrefRepository
     * @param string $value
     */
    public function __construct(SiteRepositoryInterface $siteRepository, UploadPrefRepositoryInterface $uploadPrefRepository, $value = '')
    {
        $this->siteRepository = $siteRepository;
        $this->uploadPrefRepository = $uploadPrefRepository;
        $this->originalRawValue = $value;
    }

    /**
     * Update the text value
     * @param string $value
     */
    public function setValue($value)
    {
        $this->newParsedValue = $value;

        $this->newRawValue = null;
    }

    /**
     * Get the raw value, w/ unparsed {page} {filedir} and {assets} tags
     * @return string
     */
    public function getValue()
    {
        if (is_null($this->newParsedValue)) {
            return $this->originalRawValue;
        }

        if (is_null($this->newRawValue)) {
            $this->newRawValue = $this->unparse($this->newParsedValue);
        }

        return $this->newRawValue;
    }

    /**
     * Get the parsed text value, with {page} {filedir} and {assets} tags parsed
     * @return string
     */
    public function __toString()
    {
        if (! is_null($this->newParsedValue)) {
            return $this->newParsedValue;
        }

        if (is_null($this->originalParsedValue)) {
            $this->originalParsedValue = $this->parse($this->originalRawValue);
        }

        return $this->originalParsedValue;
    }

    /**
     * Add an Asset to this object so that references can be
     * "unparsed" properly
     *
     * @param \rsanchez\Deep\Model\Asset $asset
     */
    public function addAsset(Asset $asset)
    {
        $this->assets[$asset->asset_id] = $asset->url;
    }

    /**
     * Convert parsed text back to raw text containing tags
     * @param  string $value
     * @return string
     */
    protected function unparse($value)
    {
        if ($value === null || $value === false || $value === '') {
            return '';
        }

        foreach ($this->siteRepository->getPageUris() as $entryId => $pageUri) {
            $value = str_replace($pageUri, sprintf('{page_%s}', $entryId), $value);
        }

        foreach ($this->assets as $assetId => $url) {
            $value = str_replace($url, sprintf('{assets_%s:%s}', $assetId, $url), $value);
        }

        foreach ($this->uploadPrefRepository->getUploadPrefs() as $uploadPref) {
            $url = $uploadPref->url;

            if ($url === '/' || ! $url) {
                continue;
            }

            $value = str_replace($url, sprintf('{filedir_%s}', $uploadPref->id), $value);
        }

        return $value;
    }

    /**
     * Parse text for {page} {filedir} and {assets} tags
     * @param  string $value
     * @return string
     */
    protected function parse($value)
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
        preg_match_all('#{assets_(\d+):(.*?)}#', $value, $assetsMatches);

        foreach ($assetsMatches[2] as $i => $url) {
            $this->assets[$assetsMatches[1][$i]] = $url;

            $value = str_replace($assetsMatches[0][$i], $url, $value);
        }

        return $value;
    }
}
