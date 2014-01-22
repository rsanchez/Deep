<?php

namespace rsanchez\Entries\Channel\Field;

use rsanchez\Entries\Channel\Field\Group;
use rsanchez\Entries\Channel\Field\GroupFactory;
use rsanchez\Entries\Channel\Field\Factory as FieldFactory;
use rsanchez\Entries\Channel\Field\Storage as FieldStorage;
use \IteratorAggregate;
use \ArrayIterator;

class Groups implements IteratorAggregate
{
    private $groups = array();
    private $groupsById = array();

    public function __construct(FieldStorage $storage, GroupFactory $groupFactory, FieldFactory $fieldFactory)
    {
        foreach ($storage() as $id => $fieldData) {
            $group = $groupFactory();

            foreach ($fieldData as $fieldRow) {
                $field = $fieldFactory($fieldRow);

                $group->push($field);
            }

            $this->push($group);
        }
    }

    public function push(Group $group)
    {
        array_push($this->groups, $group);

        $this->groupsById[$group->group_id] =& $group;
    }

    public function find($id)
    {
        if (! array_key_exists($id, $this->groupsById)) {
            throw new \Exception('invalid group id');
        }

        return $this->groupsById[$id];
    }

    public function getIterator()
    {
        return new ArrayIterator($this->groups);
    }
}
