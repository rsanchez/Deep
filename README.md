# Deep

A set of Eloquent models for ExpressionEngine Channel Entries. This library has a few goals in mind:

- replicate as much of the `{exp:channel:entries}` functionality as possible using Eloquent query scopes
- chainable with standard Eloquent model methods (ex. `->where('foo', 'bar')`)
- minimize the number of queries needed using eager loading
- provide an base plugin from which EE plugins/modules can extend, which has parity with `{exp:channel:entries}`

```
<?php

use rsanchez\Deep\Model\Entry

$entries = Entry::channelName('blog')
                ->limit(10)
                ->showFutureEntries()
                ->get()
?>

<?php foreach ($entries as $entry) : ?>
<article>
    <h1><?php echo $entry->title ?></h1>

    <p class="date"><?php echo $entry->entry_date->format('F j, Y') ?></p>

    <?php echo $entry->description ?>
</article>
<?php endforeach ?>
```

## Installation

Add this to your `composer.json`:

    "minimum-stability": "dev",
    "require": {
        "rsanchez/deep": "dev-develop"
    }

Make sure you load composer's autoloader at the top of your `config.php` (your actual vendor path may vary):

    require_once FCPATH.'vendor/autoload.php'

## Scopes

Scopes are how you can filter your query results. They should look familiar, since most of them relate to a native `{exp:channel:entries}` parameter.

## Entry objects

Each entry object has the following string properties from the `exp_channel_titles` table. All of the date fields (`entry_date`, `expiration_date`, and `edit_date`) are `DateTime` objects. The rest of these properties are strings.

```
$entry->entry_id
$entry->site_id
$entry->channel_id
$entry->author_id
$entry->forum_topic_id
$entry->ip_address
$entry->title
$entry->url_title
$entry->status
$entry->versioning_enabled
$entry->view_count_one
$entry->view_count_two
$entry->view_count_three
$entry->view_count_four
$entry->allow_comments
$entry->sticky
$entry->year
$entry->month
$entry->day
$entry->comment_total
```

### Dates

Entries have the following date properties. Each of these will be a `DateTime` object. `expiration_date`, `comment_expiration_date` and `recent_comment_date` can be `null`.

```
$entry->entry_date
$entry->edit_date
$entry->expiration_date
$entry->comment_expiration_date
$entry->recent_comment_date
```

### Channel object

If you need info about the entry's channel, there is the `$entry->channel` object. The channel object contains the following properties from the `exp_channels` table.

```
$entry->channel->channel_id
$entry->channel->site_id
$entry->channel->channel_name
$entry->channel->channel_title
$entry->channel->channel_url
$entry->channel->channel_description
$entry->channel->channel_lang
$entry->channel->total_entries
$entry->channel->total_comments
$entry->channel->last_entry_date
$entry->channel->last_comment_date
$entry->channel->cat_group
$entry->channel->status_group
$entry->channel->deft_status
$entry->channel->field_group
$entry->channel->search_excerpt
$entry->channel->deft_category
$entry->channel->deft_comments
$entry->channel->channel_require_membership
$entry->channel->channel_max_chars
$entry->channel->channel_html_formatting
$entry->channel->channel_allow_img_urls
$entry->channel->channel_auto_link_urls
$entry->channel->channel_notify
$entry->channel->channel_notify_emails
$entry->channel->comment_url
$entry->channel->comment_system_enabled
$entry->channel->comment_require_membership
$entry->channel->comment_use_captcha
$entry->channel->comment_moderate
$entry->channel->comment_max_chars
$entry->channel->comment_timelock
$entry->channel->comment_require_email
$entry->channel->comment_text_formatting
$entry->channel->comment_html_formatting
$entry->channel->comment_allow_img_urls
$entry->channel->comment_auto_link_urls
$entry->channel->comment_notify
$entry->channel->comment_notify_authors
$entry->channel->comment_notify_emails
$entry->channel->comment_expiration
$entry->channel->search_results_url
$entry->channel->show_button_cluster
$entry->channel->rss_url
$entry->channel->enable_versioning
$entry->channel->max_revisions
$entry->channel->default_entry_title
$entry->channel->url_title_prefix
$entry->channel->live_look_template
```

### Custom Fields

Entries have their custom fields as properties, keyed by the field short name. Most custom field properties contain the string data from the corresponding `exp_channel_data` `field_id_X` column.

```
$entry->your_field_name
```

For the following fieldtypes, an entry's custom field properties will be special objects, rather than string data from the `exp_channel_data` table.

### Matrix

Matrix

## Parameters to be implemented in the future

- category
- category_group
- search:field_name
- show_pages

## Parameters not implemented

- backspace
- cache
- refresh
- dynamic
- dynamic_start
- require_entry
- paginate
- paginate_base
- paginate_type
- track_views

## Todo

- Better array/json serialization
- Base plugin
- Pagination? probably have to wait until Illuminate paginate becomes more decoupled
- PHPdoc
- API docs
- unit tests