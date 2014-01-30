<?php

namespace rsanchez\Entries;

use rsanchez\Entries\Channel\Channel;
use rsanchez\Entries\Entries;
use rsanchez\Entries\Entity;
use rsanchez\Entries\Entity\Field;
use rsanchez\Entries\Entity\Field\Collection as FieldCollection;
use \stdClass;

class Entry extends Entity
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

    public function __construct(stdClass $row, FieldCollection $fieldCollection, Entries $entries, Channel $channel)
    {
        parent::__construct($row, $fieldCollection, $entries, $channel);

        $this->channel = $channel;
        $this->entries = $entries;
    }

    public function entryDate($format = 'U')
    {
        return date($format, $this->entry_date);
    }

    public function expirationDate($format = 'U')
    {
        return $this->expiration_date ? date($format, $this->expiration_date) : null;
    }

    public function editDate($format = 'U')
    {
        $editDate = \DateTime::createFromFormat('YmdHis', $this->edit_date);

        return $editDate->format($format);
    }

    public function commentExpirationDate($format = 'U')
    {
        return $this->comment_expiration_date ? date($format, $this->comment_expiration_date) : null;
    }

    public function recentCommentDate($format = 'U')
    {
        return $this->recent_comment_date ? date($format, $this->recent_comment_date) : null;
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
