<?php

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use rsanchez\Deep\Model\Channel;

class Entry extends Model
{
    protected $table = 'channel_titles';
    protected $primaryKey = 'entry_id';

    protected $fieldsByName = array();

    protected static $tables = array(
        'members' => array('members.member_id', 'channel_titles.author_id'),
        'channels' => array('channels.channel_id', 'channel_titles.channel_id'),
    );

    public function channel()
    {
        return $this->belongsTo('\\rsanchez\\Deep\\Model\\Channel');
    }

    public function newQuery($excludeDeleted = true)
    {
        $query = parent::newQuery($excludeDeleted);

        $query->with('channel', 'channel.fields', 'channel.fields.fieldtype');

        $query->join('channel_data', 'channel_titles.entry_id', '=', 'channel_data.entry_id');

        return $query;
    }

    public function newCollection(array $models = array())
    {
        $collection = new EntryCollection($models);

        if ($models) {
            $collection->hydrate();
        }

        return $collection;
    }

    /**
     * if you need to make custom classes
     */
    /*
    public function newInstance($attributes = array(), $exists = false)
    {
        // This method just provides a convenient way for us to generate fresh model
        // instances of this current model. It is particularly useful during the
        // hydration of new objects via the Eloquent query builder instances.
        $model = new static((array) $attributes);

        $model->exists = $exists;

        return $model;
    }

    public static function create(array $attributes)
    {
        $model = new static($attributes);

        $model->save();

        return $model;
    }
    */
    public function getAttribute($key)
    {
        $this->registerFields();

        if (! array_key_exists($key, $this->attributes) && array_key_exists($key, $this->fieldsByName)) {
            $this->attributes[$key] = $this->fieldsByName[$key]->fieldtype->mutate($this, $this->fieldsByName[$key]);
        }

        return parent::getAttribute($key);
    }

    protected function registerFields()
    {
        static $fieldsRegistered = false;

        if ($fieldsRegistered === false && isset($this->channel) && isset($this->channel->fields)) {
            $fieldsRegistered = true;
            
            $fieldsByName =& $this->fieldsByName;

            $this->channel->fields->each(function ($field) use (&$fieldsByName) {
                $this->fieldsByName[$field->field_name] = $field;
            });
        }
    }

    public function save(array $options = array())
    {
        throw new \Exception('Saving is not supported');
    }


    public function scopeStatus(Builder $query, $status)
    {
        $status = is_array($status) ? $status : array($status);

        return $query->whereIn('channel_titles.status', $status);
    }

    /**
     * Channel
     */
    public function scopeChannelName(Builder $query, $channelName)
    {
        $channelName = is_array($channelName) ? $channelName : array($channelName);
        
        return $this->requireTable($query, 'channels')->whereIn('channels.channel_name', $channelName);
    }

    /**
     * Channel ID
     */
    public function scopeChannelId(Builder $query, $channelId)
    {
        $channelId = is_array($channelId) ? $channelId : array($channelId);
        
        return $query->whereIn('channel_titles.channel_id', $channelId);
    }

    /**
     * Author ID
     */
    public function scopeAuthorId(Builder $query, $authorId)
    {
        $authorId = is_array($authorId) ? $authorId : array($authorId);
        
        return $query->whereIn('channel_titles.author_id', $authorId);
    }

    /**
     * Expired Entries
     */
    public function scopeShowExpired(Builder $query, $showExpired = true)
    {
        return $query->whereRaw(
            "(`{$prefix}channel_titles`.`expiration_date` = '' OR  `{$prefix}channel_titles`.`expiration_date` > NOW())"
        );
    }

    /**
     * Future Entries
     */
    public function scopeShowFutureEntries(Builder $query, $showFutureEntries = true)
    {
        return $query->where('channel_titles.entry_date', '<=', time());
    }

    /**
     * Fixed Order
     */
    public function scopeFixedOrder(Builder $query, array $fixedOrder)
    {
        return $this->scopeEntryId($query, $fixedOrder)->whereIn('channel_titles.entry_id', $fixedOrder)
                    ->orderBy('FIELD('.implode(', ', $fixedOrder).')', 'asc');
    }

    /**
     * Sticky
     */
    public function scopeSticky(Builder $query, $sticky)
    {
        array_unshift($query->getQuery()->orders, array('channel_titles.sticky', 'desc'));

        return $query;
    }

    /**
     * Entry ID
     */
    public function scopeEntryId(Builder $query, $entryId)
    {
        $entryId = is_array($entryId) ? $entryId : array($entryId);

        return $query->whereIn('channel_titles.entry_id', $entryId);
    }

    /**
     * Not Entry ID
     */
    public function scopeNotEntryId(Builder $query, $notEntryId)
    {
        $notEntryId = is_array($notEntryId) ? $notEntryId : array($notEntryId);

        return $query->whereNotIn('channel_titles.entry_id', $notEntryId);
    }

    /**
     * Entry ID From
     */
    public function scopeEntryIdFrom(Builder $query, $entryIdFrom)
    {
        return $query->where('channel_titles.entry_id', '>=', $entryIdFrom);
    }

    /**
     * Entry ID To
     */
    public function scopeEntryIdTo(Builder $query, $entryIdTo)
    {
        return $query->where('channel_titles.entry_id', '<=', $entryIdTo);
    }

    /**
     * Member Group ID
     */
    public function scopeGroupId(Builder $query, $groupId)
    {
        $groupId = is_array($groupId) ? $groupId : array($groupId);

        return $this->requireTable($query, 'members')->whereIn('members.group_id', $groupId);
    }

    /**
     * Not Member Group ID
     */
    public function scopeNotGroupId(Builder $query, $notGroupId)
    {
        $notGroupId = is_array($notGroupId) ? $notGroupId : array($notGroupId);

        return $this->requireTable($query, 'members')->whereNotIn('members.group_id', $notGroupId);
    }

    /**
     * Limit
     */
    public function scopeLimit(Builder $query, $limit)
    {
        return $query->take($limit);
    }

    /**
     * Offset
     */
    public function scopeOffset(Builder $query, $offset)
    {
        return $query->skip($offset);
    }

    /**
     * Start On
     */
    public function scopeStartOn(Builder $query, $startOn)
    {
        return $query->where('channel_titles.entry_date', '>=', $startOn);
    }

    /**
     * Stop Before
     */
    public function scopeStopBefore(Builder $query, $stopBefore)
    {
        return $query->where('channel_titles.entry_date', '<', $stopBefore);
    }

    /**
     * URL Title
     */
    public function scopeUrlTitle(Builder $query, $urlTitle)
    {
        $urlTitle = is_array($urlTitle) ? $urlTitle : array($urlTitle);

        return $query->whereIn('channel_titles.url_title', $urlTitle);
    }

    /**
     * Username
     */
    public function scopeUsername(Builder $query, $username)
    {
        $username = is_array($username) ? $username : array($username);

        return $this->requireTable($query, 'members')->whereIn('members.username', $username);
    }

    /**
     * Search
     */
    public function scopeSearch(Builder $query, array $search)
    {
        $this->requireTable($query, 'channel_data');

        foreach ($search as $fieldName => $values) {
            try {
                $field = $this->channelFieldRepository->find($fieldName);

                $query->where(function ($query) use ($field, $values) {

                    foreach ($values as $value) {
                        $query->orWhere('channel_data.field_id_'.$field->id(), 'LIKE', '%{$value}%');
                    }

                });

            } catch (Exception $e) {
                //$e->getMessage();
            }
        }

        return $query;
    }

    /**
     * Year
     */
    public function scopeYear(Builder $query, $year)
    {
        return $query->where('channel_titles.year', $year);
    }

    /**
     * Month
     */
    public function scopeMonth(Builder $query, $month)
    {
        return $query->where('channel_titles.month', $month);
    }

    /**
     * Day
     */
    public function scopeDay(Builder $query, $day)
    {
        return $query->where('channel_titles.day', $day);
    }

    protected function requireTable(Builder $query, $which)
    {
        if (! isset(static::$tables[$which])) {
            return $query;
        }

        foreach ($query->getQuery()->joins as $joinClause) {
            if ($joinClause->table === $which) {
                return $query;
            }
        }

        return $query->join($which, static::$tables[$which][0], '=', static::$tables[$which][1]);
    }
}
