<?php

namespace rsanchez\Entries\Entries;

use rsanchez\Entries\Channel;
use rsanchez\Entries\Entries\Field;
use rsanchez\Entries\Entries\Field\Factory as FieldFactory;
use \stdClass;

class Entry {

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

  /**
   * @var \rsanchez\Entries\Channel
   */
  public $channel;

  public function __construct(Channel $channel, FieldFactory $fieldFactory, stdClass $result) {
    $this->channel = $channel;

    $properties = get_class_vars(__CLASS__);

    foreach ($properties as $property => $value) {
      if (property_exists($result, $property)) {
        $this->$property = $result->$property;
      }
    }

    foreach ($this->channel->fields as $field) {
      $property = 'field_id_'.$field->field_id;
      $value = property_exists($result, $property) ? $result->$property : '';
      $this->{$field->field_name} = $fieldFactory->createField($channel, $field, $this, $value);
    }
  }

  public function toArray() {
    return (array) $this;
  }

/*
  public function __call($name, $args) {
    if (isset($this->$name) && $this->$name instanceof Field) {
      return call_user_func_array($this->$name, $args);
    }
  }
*/

  public function url_title_path($path) {
    return '/'.($path.'/'.$this->url_title);
  }

  public function title_permalink($path) {
    return '/'.($path);
  }

  public function entry_id_path($path) {
    return '/'.($path.'/'.$this->entry_id);
  }

  public function entry_date($format) {
    return date($format, $this->entry_date);
  }

  public function edit_date($format) {
    return date($format, $this->edit_date);
  }

  public function expiration_date($format) {
    return date($format, $this->expiration_date);
  }

  public function comment_expiration_date($format) {
    return date($format, $this->comment_expiration_date);
  }

  public function recent_comment_date($format) {
    return date($format, $this->recent_comment_date);
  }
}