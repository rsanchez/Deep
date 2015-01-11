<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

/**
 * A property that does nothing
 */
class NullProperty extends AbstractProperty
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefix()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return '';
    }
}
