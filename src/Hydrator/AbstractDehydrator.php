<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Hydrator;

use Illuminate\Database\ConnectionInterface;

/**
 * Abstract Hydrator class
 *
 * Hydrators bind custom fields properties to Entry objects
 */
abstract class AbstractDehydrator implements DehydratorInterface
{
    /**
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $db;

    /**
     * @var \rsanchez\Deep\Hydrator\DehydratorCollection
     */
    protected $dehydrators;

    /**
     * Constructor
     *
     * @param \Illuminate\Database\ConnectionInterface     $db
     * @param \rsanchez\Deep\Hydrator\DehydratorCollection $dehydrators
     */
    public function __construct(ConnectionInterface $db, DehydratorCollection $dehydrators)
    {
        $this->db = $db;

        $this->dehydrators = $dehydrators;
    }
}
