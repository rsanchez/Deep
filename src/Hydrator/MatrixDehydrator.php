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

/**
 * Dehydrator for the Matrix fieldtype
 */
class MatrixDehydrator extends AbstractDehydrator
{
    /**
     * {@inheritdoc}
     */
    public function dehydrate(AbstractEntity $entity, PropertyInterface $property, AbstractEntity $parentEntity = null, PropertyInterface $parentProperty = null)
    {
        $rows = $entity->{$property->getName()};

        $rowIds = [];

        if ($rows) {
            foreach ($rows as $i => $row) {
                $row->row_order = $i + 1;

                $row->{$entity->getKeyName()} = $entity->getId();

                $row->site_id = $entity->site_id;

                // save once to make sure we have an id
                if (! $row->exists) {
                    $row->save();
                }

                $rowIds[] = $row->row_id;

                $cols = $row->getCols();

                if (is_null($cols)) {
                    $cols = $property->getChildProperties();

                    $row->setCols($cols);
                }

                foreach ($cols as $col) {
                    $dehydrator = $this->dehydrators->get($col->getType());

                    if ($dehydrator) {
                        $row->{$col->getIdentifier()} = $dehydrator->dehydrate($row, $col, $entity, $property);
                    } else {
                        $row->{$col->getIdentifier()} = $row->propertyToArray($col);
                    }
                }

                $row->save();
            }
        }

        // delete unused
        $query = $this->db->table('matrix_data');

        if ($rowIds) {
            $query->whereNotIn('row_id', $rowIds);
        }

        $query->where('entry_id', $entity->getId());

        $query->where('field_id', $property->getId());

        $query->delete();

        return $rowIds ? '1' : null;
    }
}
