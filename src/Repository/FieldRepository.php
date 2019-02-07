<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Repository;

use rsanchez\Deep\Collection\FieldCollection;
use rsanchez\Deep\Model\Field;
use rsanchez\Deep\Collection\ChannelCollection;
use Illuminate\Database\ConnectionInterface;

/**
 * Repository of all Fields
 */
class FieldRepository extends AbstractFieldRepository implements ChannelFieldRepositoryInterface
{
    /**
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $db;

    /**
     * Array of FieldCollection keyed by channel_id
     * @var array
     */
    protected $fieldsByChannel = [];

    /**
     * {@inheritdoc}
     *
     * @param \rsanchez\Deep\Model\Field               $model
     * @param \Illuminate\Database\ConnectionInterface $db
     */
    public function __construct(Field $model, ConnectionInterface $db)
    {
        parent::__construct($model);

        $this->db = $db;
    }

    protected function getFieldIdsByChannel()
    {
        $fieldIdsByChannel = [];

        $fieldAssignments = $this->db->table('channel_field_groups_fields')
            ->select('channel_id', 'field_id')
            ->join('channels_channel_field_groups', 'channels_channel_field_groups.group_id', '=', 'channel_field_groups_fields.group_id')
            ->union($this->db->table('channels_channel_fields'))
            ->get();

        foreach ($fieldAssignments as $fieldAssignment) {
            $fieldAssignment = (array) $fieldAssignment;

            if (! array_key_exists($fieldAssignment['channel_id'], $fieldIdsByChannel)) {
                $fieldIdsByChannel[$fieldAssignment['channel_id']] = [];
            }

            $fieldIdsByChannel[$fieldAssignment['channel_id']][] = $fieldAssignment['field_id'];
        }

        return $fieldIdsByChannel;
    }

    /**
     * {@inheritdoc}
     */
    protected function loadCollection()
    {
        if (is_null($this->collection)) {
            $fieldIdsByChannel = $this->getFieldIdsByChannel();

            $this->collection = $this->model
                ->orderByRaw("field_type IN ('matrix', 'grid') DESC")
                ->orderByRaw("field_type IN ('playa', 'relationship') DESC")
                ->orderBy('field_order', 'asc')
                ->get();

            foreach ($this->collection as $field) {
                foreach ($fieldIdsByChannel as $channelId => $fieldIds) {
                    if (in_array($field->field_id, $fieldIds)) {
                        if (! array_key_exists($channelId, $this->fieldsByChannel)) {
                            $this->fieldsByChannel[$channelId] = new FieldCollection();
                        }

                        $this->fieldsByChannel[$channelId]->push($field);
                    }
                }

                $this->fieldsByName[$field->field_name] = $field;
                $this->fieldsById[$field->field_id] = $field;
            }
        }
    }

    /**
     * Get a Collection of fields from the specified group
     *
     * @param  int                                       $channelId
     * @return \rsanchez\Deep\Collection\FieldCollection
     */
    public function getFieldsByChannel($channelId)
    {
        $this->loadCollection();

        return $channelId && isset($this->fieldsByChannel[$channelId]) ? $this->fieldsByChannel[$channelId] : new FieldCollection();
    }

    /**
     * Get the fields used by the channels in the specified collection
     * @param  \rsanchez\Deep\Collection\ChannelCollection $channels
     * @param  array                                       $withFields fields names to include
     * @return \rsanchez\Deep\Collection\FieldCollection
     */
    public function getFieldsByChannelCollection(ChannelCollection $channels, $withFields = [])
    {
        $this->loadCollection();

        $fields = new FieldCollection();

        foreach ($channels as $channel) {
            foreach ($channel->fields as $field) {
                if ($withFields && !in_array($field->field_name, $withFields)) {
                    continue;
                }

                $fields->push($field);
            }
        }

        return $fields;
    }
}
