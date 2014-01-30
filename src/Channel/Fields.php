<?php

namespace rsanchez\Deep\Channel;

use rsanchez\Deep\Channel\Field\Field;
use rsanchez\Deep\Channel\Field\Group;
use rsanchez\Deep\Channel\Field\Collection;
use rsanchez\Deep\Channel\Field\GroupFactory;
use rsanchez\Deep\Channel\Field\Factory as FieldFactory;
use rsanchez\Deep\Channel\Field\Storage as FieldStorage;
use IteratorAggregate;
use ArrayIterator;

class Fields extends Collection
{
    private $groups = array();
    private $groupsById = array();

    public function __construct(FieldStorage $storage, GroupFactory $groupFactory, FieldFactory $fieldFactory)
    {
        foreach ($storage() as $id => $fieldData) {
            $group = $groupFactory->createGroup($id);

            foreach ($fieldData as $fieldRow) {
                $field = $fieldFactory->createProperty($fieldRow);

                $this->push($field);

                $group->push($field);
            }

            $this->pushGroup($group);
        }
    }

    public function pushGroup(Group $group)
    {
        array_push($this->groups, $group);

        $this->groupsById[$group->group_id] =& $group;
    }

    public function findGroup($id)
    {
        if (! array_key_exists($id, $this->groupsById)) {
            throw new \Exception('invalid group id');
        }

        return $this->groupsById[$id];
    }
}
