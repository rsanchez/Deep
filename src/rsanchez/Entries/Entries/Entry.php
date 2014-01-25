<?php

namespace rsanchez\Entries\Entries;

use rsanchez\Entries\Channel;
use rsanchez\Entries\Entries;
use rsanchez\Entries\Entries\Field;
use rsanchez\Entries\Entries\Field\Factory as FieldFactory;
use Carbon\Carbon;
use \stdClass;

class Entry
{
    public $entry_id;
    public $site_id;
    public $channel_id;
    public $author_id;
    public $forum_topic_id;
    public $ip_address;
    public $title;
    public $url_title;
    public $status;
    public $versioning_enabled;
    public $view_count_one;
    public $view_count_two;
    public $view_count_three;
    public $view_count_four;
    public $allow_comments;
    public $sticky;
    public $entry_date;
    public $year;
    public $month;
    public $day;
    public $expiration_date;
    public $comment_expiration_date;
    public $edit_date;
    public $recent_comment_date;
    public $comment_total;

    protected $methodAliases = array(
        'entry_id_path'           => 'entryIdPath',
        'url_title_path'          => 'urlTitlePath',
        'title_permalink'         => 'titlePermalink',
        'entry_date'              => 'entryDate',
        'edit_date'               => 'editDate',
        'expiration_date'         => 'expirationDate',
        'comment_expiration_date' => 'commentExpirationDate',
        'recent_comment_date'     => 'recentCommentDate',
    );

    /**
     * @var \rsanchez\Entries\Channel
     */
    public $channel;
    protected $entries;

    public function __construct(Entries $entries, Channel $channel, FieldFactory $fieldFactory, stdClass $result)
    {
        $this->channel = $channel;
        $this->entries = $entries;

        $properties = get_class_vars(__CLASS__);

        foreach ($properties as $property => $value) {
            if (property_exists($result, $property)) {
                $this->$property = $result->$property;
            }
        }

        $this->entry_date = Carbon::createFromFormat('U', $this->entry_date);

        if ($this->expiration_date) {
            $this->expiration_date = Carbon::createFromFormat('U', $this->expiration_date);
        }

        if ($this->comment_expiration_date) {
            $this->comment_expiration_date = Carbon::createFromFormat('U', $this->comment_expiration_date);
        }

        if ($this->recent_comment_date) {
            $this->recent_comment_date = Carbon::createFromFormat('U', $this->recent_comment_date);
        }

        if ($this->edit_date) {
            $this->edit_date = Carbon::createFromFormat('YmdHis', $this->edit_date);
        }

        foreach ($this->channel->fields as $field) {
            $property = 'field_id_'.$field->field_id;
            $value = property_exists($result, $property) ? $result->$property : '';
            $this->{$field->field_name} = $fieldFactory->createField($channel, $field, $this, $value);
        }
    }

    public function toArray()
    {
        return (array) $this;
    }


    public function __call($name, $args)
    {
        /*
        if (isset($this->$name) && $this->$name instanceof Field) {
            return call_user_func_array($this->$name, $args);
        }
        */
        if (array_key_exists($name, $this->methodAliases)) {
            return call_user_func_array(array($this, $this->methodAliases[$name]), $args);
        }
    }

    public function entryDate($format = 'U')
    {
        return $this->entry_date->format($format);
    }

    public function expirationDate($format = 'U')
    {
        if (! $this->expiration_date) {
            return null;
        }

        return $this->expiration_date->format($format);
    }

    public function editDate($format = 'U')
    {
        if (! $this->edit_date) {
            return null;
        }

        return $this->edit_date->format($format);
    }

    public function commentExpirationDate($format = 'U')
    {
        if (! $this->comment_expiration_date) {
            return null;
        }

        return $this->comment_expiration_date->format($format);
    }

    public function recentCommentDate($format = 'U')
    {
        if (! $this->recent_comment_date) {
            return null;
        }

        return $this->recent_comment_date->format($format);
    }

    public function urlTitlePath($path)
    {
        return $this->entries->baseUrl().$path.'/'.$this->url_title;
    }

    public function titlePermalink($path)
    {
        return $this->entries->baseUrl().$path.'/'.$this->url_title;
    }

    public function entryIdPath($path)
    {
        return $this->entries->baseUrl().$path.'/'.$this->entry_id;
    }
}
