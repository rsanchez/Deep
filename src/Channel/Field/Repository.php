<?php

namespace rsanchez\Deep\Channel\Field;

use rsanchez\Deep\Channel\Field\Field;
use rsanchez\Deep\Channel\Field\Group;
use rsanchez\Deep\Channel\Field\Collection;
use rsanchez\Deep\Channel\Field\GroupFactory;
use rsanchez\Deep\Channel\Field\Factory as FieldFactory;
use rsanchez\Deep\Channel\Field\Storage as FieldStorage;
use SplObjectStorage;

class Repository extends Collection
{
    private $groups;
    private $groupsById = array();

    public function __construct(FieldStorage $storage, GroupFactory $groupFactory, FieldFactory $fieldFactory)
    {
        $this->groups = new SplObjectStorage();

        foreach ($storage() as $id => $fieldData) {
            $group = $groupFactory->createGroup($id);

            foreach ($fieldData as $fieldRow) {
                $field = $fieldFactory->createProperty($fieldRow);

                $this->attach($field);

                $group->attach($field);
            }

            $this->attachGroup($group);
        }
    }

    public function attachGroup(Group $group)
    {
        $this->groups->attach($group);

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
