<?php

namespace rsanchez\Entries\Channel;

use rsanchez\Entries\Channel\Field;
use rsanchez\Entries\Channel\Field\Group;
use rsanchez\Entries\Channel\Field\Collection;
use rsanchez\Entries\Channel\Field\GroupFactory;
use rsanchez\Entries\Channel\Field\Factory as FieldFactory;
use rsanchez\Entries\Channel\Field\Storage as FieldStorage;
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
