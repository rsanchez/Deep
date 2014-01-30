<?php

namespace rsanchez\Entries\Channel;

use rsanchez\Entries\Channel\Field\Group as FieldGroup;
use rsanchez\Entries\IoC;
use stdClass;

class Channel
{
    public $channel_id;
    public $site_id;
    public $channel_name;
    public $channel_title;
    public $channel_url;
    public $channel_description;
    public $channel_lang;
    public $total_entries;
    public $total_comments;
    public $last_entry_date;
    public $last_comment_date;
    public $cat_group;
    public $status_group;
    public $deft_status;
    public $field_group;
    public $search_excerpt;
    public $deft_category;
    public $deft_comments;
    public $channel_require_membership;
    public $channel_max_chars;
    public $channel_html_formatting;
    public $channel_allow_img_urls;
    public $channel_auto_link_urls;
    public $channel_notify;
    public $channel_notify_emails;
    public $comment_url;
    public $comment_system_enabled;
    public $comment_require_membership;
    public $comment_use_captcha;
    public $comment_moderate;
    public $comment_max_chars;
    public $comment_timelock;
    public $comment_require_email;
    public $comment_text_formatting;
    public $comment_html_formatting;
    public $comment_allow_img_urls;
    public $comment_auto_link_urls;
    public $comment_notify;
    public $comment_notify_authors;
    public $comment_notify_emails;
    public $comment_expiration;
    public $search_results_url;
    public $show_button_cluster;
    public $rss_url;
    public $enable_versioning;
    public $max_revisions;
    public $default_entry_title;
    public $url_title_prefix;
    public $live_look_template;

    public $fields;
    public $fieldNames = array();

    public function __construct(FieldGroup $fieldGroup, stdClass $row)
    {
        $this->fields = $fieldGroup;

        $properties = get_class_vars(__CLASS__);

        foreach ($properties as $property => $value) {
            if (property_exists($row, $property)) {
                $this->$property = $row->$property;
            }
        }

        foreach ($this->fields as $field) {
            $this->fieldNames[] = $field->name();
        }
    }

    public function toArray()
    {
        return (array) $this;
    }

    public function hasField($name)
    {
        return array_key_exists($name, $this->fieldNames);
    }
}
