# Deep

A set of [Eloquent](http://laravel.com/docs/eloquent) models for ExpressionEngine Channel Entries. This library has a few goals in mind:

- replicate as much of the `{exp:channel:entries}` functionality as possible using Eloquent [query scopes](http://laravel.com/docs/eloquent#query-scopes)
- chainable with standard Eloquent model methods (ex. `->where('foo', 'bar')`)
- minimize the number of queries needed using eager loading
- provide an base plugin from which EE plugins/modules can extend, which has near parity with `{exp:channel:entries}`
- automatically fetch custom fields using field names and entities instead of just raw text from `exp_channel_data`

```
<?php

use rsanchez\Deep\App\Entries;

$entries = Entries::channel('blog')
                ->limit(10)
                ->showFutureEntries()
                ->get()
?>

<?php foreach ($entries as $entry) : ?>
<article>
    <h1><?php echo $entry->title; ?></h1>

    <p class="date"><?php echo $entry->entry_date->format('F j, Y'); ?></p>

    <?php echo $entry->description; ?>
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

## Query Scopes

Query scopes are how you can filter your query results. They should look familiar, since most of them relate to a native `{exp:channel:entries}` parameter.

### Channel Name

```
Entries::channel('blog', 'news')->get();
```

### Not Channel Name

```
Entries::notChannel('blog', 'news')->get();
```

### Channel ID

```
Entries::channelId(1, 2)->get();
```

### Not Channel ID

```
Entries::notChannelId(1, 2)->get();
```

### Author ID

```
Entry::authorId(1, 2)->get();
```

### Not Author ID

```
Entry::notAuthorId(1, 2)->get();
```

### Category ID

```
Entry::category(1, 2)->get();
```

### Not Category ID

```
Entry::notCategory(1, 2)->get();
```

### Category Name

```
Entry::categoryName('mammals', 'reptiles')->get();
```

### NotCategory Name

```
Entry::notCategoryName('mammals', 'reptiles')->get();
```

### Category Group

```
Entry::categoryGroup(1, 2)->get();
```

### Not Category Group

```
Entry::notCategoryGroup(1, 2)->get();
```

### Day

```
Entry::day(31)->get();
```

### Entry ID

```
Entry::entryId(1, 2)->get();
```

### Not Entry ID

```
Entry::notEntryId(1, 2)->get();
```

### Entry ID From

```
Entry::entryIdFrom(1)->get();
```

### Entry ID To

```
Entry::entryIdTo(100)->get();
```

### Fixed Order

```
Entry::fixedOrder(4, 8, 15, 16, 23, 42)->get();
```

### Member Group ID

```
Entry::groupId(1, 2)->get();
```

### Not Member Group ID

```
Entry::notGroupId(1, 2)->get();
```

### Limit

```
Entry::limit(1)->get();
```

### Month

```
Entry::month(12)->get();
```

### Offset

```
Entry::offset(1)->get();
```

### Year

```
Entry::year(2014)->get();
```

## Entry objects

Each entry object has the following string properties from the `exp_channel_titles` table.

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

Entries have the following date properties. Each of these will be a (`Carbon` object)[https://github.com/briannesbitt/Carbon]. `expiration_date`, `comment_expiration_date` and `recent_comment_date` can be `null`.

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

Entries have their custom fields as properties, keyed by the field short name. Most custom field properties merely the string data from the corresponding `exp_channel_data` `field_id_X` column.

```
$entry->your_field_name
```

For the following fieldtypes, an entry's custom field properties will be special objects, rather than string data from the `exp_channel_data` table.

### Matrix & Grid

Matrix & Grid fields will be Eloquent Collections of `Row` objects. Each `Row` object has string properties keyed to the column short name from the `exp_matrix_data` and `exp_channel_grid_field_X` tables, respectively. Custom `Row` fields follow the same logic as `Entry` custom fields.

```
$number_of_rows = $entry->your_matrix_field->count();

foreach ($entry->your_matrix_field as $row) {
    echo $row->your_text_column;
    foreach ($row->your_playa_column as $childEntry) {
        echo $childEntry->title;
    }
}
```

### Playa & Relationship

Playa & Relationship fields will be Eloquent Collections of related `Entry` objects. These `Entry` objects behave just as parent `Entry` objects do.

```
$number_of_rows = $entry->your_playa_field->count();

foreach ($entry->your_playa_field as $childEntry) {
    echo $childEntry->title;
}
```

### Assets

Assets fields will be Eloquent Collections of `Asset` objects. `Asset` objects have the following properties:

```
foreach ($entry->your_assets_field as $file) {
    $file->url
    $file->server_path
    $file->file_id
    $file->folder_id
    $file->source_type
    $file->source_id
    $file->filedir_id
    $file->file_name
    $file->title
    $file->date
    $file->alt_text
    $file->caption
    $file->author
    $file->desc
    $file->location
    $file->keywords
    $file->date_modified
    $file->kind
    $file->width
    $file->height
    $file->size
    $file->search_keywords
}
```

### File

File fields will be a single `File` object. `File` objects have the following properties:

```
$entry->your_file_field->url
$entry->your_file_field->server_path
$entry->your_file_field->file_id
$entry->your_file_field->site_id
$entry->your_file_field->title
$entry->your_file_field->upload_location_id
$entry->your_file_field->rel_path
$entry->your_file_field->mime_type
$entry->your_file_field->file_name
$entry->your_file_field->file_size
$entry->your_file_field->description
$entry->your_file_field->credit
$entry->your_file_field->location
$entry->your_file_field->uploaded_by_member_id
$entry->your_file_field->upload_date
$entry->your_file_field->modified_by_member_id
$entry->your_file_field->modified_date
$entry->your_file_field->file_hw_original
```

```
echo '<img src="'.$entry->your_file_field->url.'" />';
```

### Date

Date fields will be a single [`Carbon` object](https://github.com/briannesbitt/Carbon).

```
echo $entry->your_date_field->format('Y-m-d H:i:s');
```

## Extending the `BasePlugin` class

The abstract `rsanchez\Deep\Plugin\BasePlugin` class is provided as a base for ExpressionEngine modules and plugins. The `parse` method parses a template using an `EntryCollection`.

```
<?php

use rsanchez\Deep\Entries;
use rsanchez\Deep\Plugin\BasePlugin;

class My_plugin extends BasePlugin
{
    public function __construct()
    {
        $entries = Entries::tagparams(ee()->TMPL->tagparams)
                            // do any additional custom querying here
                            ->get();

        $this->return_data = $this->parse($entries);
    }
}

```

## The `Titles` Class

You might be wondering how to do the equivalent of `disable="custom_fields"`. You can use the `Titles` class for this, which will not query for custom fields.

```
<?php

use rsanchez\Deep\App\Titles;

$entries = Titles::channel('blog')
                ->limit(1)
                ->get();
```

## Parameters not implemented

- backspace
- cache
- display_by
- disable
- dynamic
- dynamic_start
- month_limit
- paginate
- paginate_base
- paginate_type
- refresh
- related_categories_mode
- relaxed_categories
- require_entry
- show_current_week
- track_views
- week_sort
- uncategorized_entries

## Todo

- Category scope
- orderby/sort
- Pagination? probably have to wait until Illuminate paginate becomes more decoupled
- API docs
- unit tests
