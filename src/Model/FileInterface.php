<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

/**
 * Interface for file-based models like File and Asset
 */
interface FileInterface
{
    /**
     * Get the public url to this file
     * @return string
     */
    public function getUrlAttribute();

    /**
     * Get the server path to this file
     * @return string
     */
    public function getServerPathAttribute();

    /**
     * Get the public url to this file
     * @return string
     */
    public function __toString();
}
