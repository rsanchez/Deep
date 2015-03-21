# Deep

[![Build Status](https://travis-ci.org/rsanchez/Deep.svg?branch=master)](https://travis-ci.org/rsanchez/Deep)
[![Total Downloads](https://poser.pugx.org/rsanchez/deep/downloads.png)](https://packagist.org/packages/rsanchez/deep)
[![Latest Stable Version](https://poser.pugx.org/rsanchez/deep/v/stable.png)](https://packagist.org/packages/rsanchez/deep)

A read-only set of [Eloquent](http://laravel.com/docs/eloquent) models for ExpressionEngine Channel Entries. This library has a few goals in mind:

- replicate as much of the `{exp:channel:entries}` functionality as possible using Eloquent [query scopes](http://laravel.com/docs/eloquent#query-scopes)
- chainable with standard Eloquent model methods (ex. `->where('foo', 'bar')`)
- minimize the number of queries needed using eager loading
- provide an base plugin from which EE plugins/modules can extend, which has near parity with `{exp:channel:entries}`
- automatically fetch custom fields using field names and entities instead of just raw text from `exp_channel_data`

For more detailed information, see the [auto-generated API docs](http://rsanchez.github.io/Deep/api).

```
<?php

// this particular wrapper class is for use *inside* EE
use rsanchez\Deep\App\EE\Entries;

$entries = Entries::channel('blog')
                ->limit(10)
                ->showFutureEntries()
                ->get();
?>

<?php foreach ($entries as $entry) : ?>
<article>
    <h1><?php echo e($entry->title); ?></h1>

    <p class="date"><?php echo $entry->entry_date->format('F j, Y'); ?></p>

    <?php echo $entry->description; ?>
</article>
<?php endforeach ?>
```

## Installation

Run this command in your terminal:

    composer require rsanchez/deep

## Setup

### ExpressionEngine

Make sure you load composer's autoloader at the top of your `config.php` (your actual vendor path may vary):

    require_once FCPATH.'vendor/autoload.php'

Then you can create your own plugin that uses Deep by [extending the `BasePlugin` class](#extending-the-baseplugin-class). Or you can use the built-in wrapper class, which bootstraps Deep with EE for you:

```
use rsanchez\Deep\App\EE\Entries;

$entries = Entries::channel('blog')
                ->limit(10)
                ->get();
```

If you are not [extending the `BasePlugin` class](#extending-the-baseplugin-class) or using the `Entries` or `Titles` wrapper class, you will need to boot up Eloquent to use EE's database connection. You should do so in your constructor. This method is idempotent, so you can safely run it more than once without consequence.

    \rsanchez\Deep\Deep::bootEE(ee());

### Laravel

Deep comes with a service provider for Laravel. Add this to the list of providers in `app/config/app.php`:

    'rsanchez\Deep\App\Laravel\ServiceProvider',

This registers the `Entries`, `Titles` and `Categories` facades, so you can use them in your app easily:

    Route::get('/blog', function()
    {
        $entries = Entries::channel('blog')->get();
        return View::make('blog.index')->withEntries($entries);
    });

    Route::get('/blog/json', function()
    {
        $entries = Entries::channel('blog')->get();
        return Response::json($entries);
    });

If you are using a table prefix for your database tables (EE uses `exp_` by default, so you most likely are), make sure to set the prefix in Laravel's `app/config/database.php`

If you need to use a DB connection other than Laravel's default connection, you should add the following configuration to `app/config/database.php`:

    'deep' => array(
        'connection' => 'your_connection_name',
    ),

The specified connection will be used for all of Deep's models.

### Generic PHP (or other framework)

First you must bootstrap Eloquent for use outside of Laravel. There are [many](https://laracasts.com/lessons/how-to-use-eloquent-outside-of-laravel) [guides](http://www.slimframework.com/news/slim-and-laravel-eloquent-orm) [out](http://www.edzynda.com/use-laravels-eloquent-orm-outside-of-laravel/) [there](http://jenssegers.be/blog/53/using-eloquent-without-laravel) on how to do this.

Then you can simply use the generic wrapper:

```
use rsanchez\Deep\App\Entries;

$entries = Entries::channel('blog')
                ->limit(10)
                ->get();
```

Or instantiate your own instance of the Deep DI container if you prefer:

```
use rsanchez\Deep\Deep;

$deep = new Deep();

$entries = $deep->make('Entry')
                ->channel('blog')
                ->limit(10)
                ->get();
```


### Using the Phar archive for easier distribution

You can build a Phar archive as an alternative installation method. The best way to package Deep with your custom distributed add-on is to use the Phar archive, since EE doesn't natively support compser installation out of the box.

To build the Phar archive, you must have [box](http://box-project.org/) installed. Then you can clone this repo, run `composer install` to fetch all the dependencies, and run `box build` to create the Phar archive. The archive can be found in `build/deep.phar` after it's built.

Now you can package that single Phar archive with your add-on (say, in a `phar` folder in your add-on root) and load it like so:

```
// this is a courtesy check in case other add-ons are also
// using deep.phar
if ( ! class_exists('\\rsanchez\\Deep\\Deep'))
{
    require_once PATH_THIRD.'your_addon/phar/deep.phar';
}
```

## Query Scopes

### Filtering Scopes

Filtering scopes should look familiar, since most of them relate to a native `{exp:channel:entries}` parameter.

#### Channel Name

```
Entries::channel('blog', 'news')->get();
```

#### Not Channel Name

```
Entries::notChannel('blog', 'news')->get();
```

#### Channel ID

```
Entries::channelId(1, 2)->get();
```

#### Not Channel ID

```
Entries::notChannelId(1, 2)->get();
```

#### Author ID

```
Entries::authorId(1, 2)->get();
```

#### Not Author ID

```
Entries::notAuthorId(1, 2)->get();
```

#### Category ID

```
Entries::category(1, 2)->get();
```

#### Not Category ID

```
Entries::notCategory(1, 2)->get();
```

#### All Categories

Only show entries that have all of the specified categories.

```
Entries::allCategories(1, 2)->get();
```

#### Not All Categories

Exclude entries that have all of the specified categories.

```
Entries::notAllCategories(1, 2)->get();
```

#### Category Name

```
Entries::categoryName('mammals', 'reptiles')->get();
```

#### Not Category Name

```
Entries::notCategoryName('mammals', 'reptiles')->get();
```

#### Category Group

```
Entries::categoryGroup(1, 2)->get();
```

#### Not Category Group

```
Entries::notCategoryGroup(1, 2)->get();
```

#### Day

```
Entries::day(31)->get();
```

#### Dynamic Parameters

```
Entries::dynamicParameters(array('limit', 'search:your_field_name'), $_REQUEST)->get();
```

#### Entry ID

```
Entries::entryId(1, 2)->get();
```

#### Not Entry ID

```
Entries::notEntryId(1, 2)->get();
```

#### Entry ID From

```
Entries::entryIdFrom(1)->get();
```

#### Entry ID To

```
Entries::entryIdTo(100)->get();
```

#### Fixed Order

```
Entries::fixedOrder(4, 8, 15, 16, 23, 42)->get();
```

#### Member Group ID

```
Entries::groupId(1, 2)->get();
```

#### Not Member Group ID

```
Entries::notGroupId(1, 2)->get();
```

#### Limit

```
Entries::limit(1)->get();
```

#### Month

```
Entries::month(12)->get();
```

#### Offset

```
Entries::offset(1)->get();
```

#### Show Expired

```
Entries::showExpired(false)->get();
```

#### Show Future Entries

```
Entries::showFutureEntries(true)->get();
```

#### Show Pages

```
Entries::showPages(false)->get();
```

#### Show Pages Only

```
Entries::showPagesOnly(true)->get();
```

#### Site ID

```
Entries::siteId(1, 2)->get();
```

#### Start On

Unix time:

```
Entries::startOn(1394393247)->get();
```

Or use a `DateTime` object:

```
$date = new DateTime();
Entries::startOn($date)->get();
```

#### Stop Before

Unix time:

```
Entries::stopBefore(1394393247)->get();
```

Or use a `DateTime` object:

```
$date = new DateTime();
Entries::stopBefore($date)->get();
```

#### Sticky

```
Entries::sticky(true)->get();
```

#### Status

```
Entries::status('open', 'closed')->get();
```

#### Not Status

```
Entries::notStatus('open', 'closed')->get();
```

#### URL Title

```
Entries::urlTitle('cats', 'dogs')->get();
```

#### Not URL Title

```
Entries::notUrlTitle('cats', 'dogs')->get();
```

#### Username

```
Entries::username('john_doe', 'jane_doe')->get();
```

#### Not Username

```
Entries::notUsername('john_doe', 'jane_doe')->get();
```

#### Year

```
Entries::year(2014)->get();
```

#### Tagparams

This scope accepts an array of parameters And applies all the [supported](#parameters-not-implemented) `{exp:channel:entries}` parameters to the query.

```
Entries::tagparams(ee()->TMPL->tagparams)->get();
```

The following channel:entries parameters are not implemented by the `tagparams` scope:

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

### Eager Loading Scopes

These scopes force eager loading of certain relationships. Eager loading of custom field data is done automatically with the `Entry` model (and the `Entries` proxy). Use the `Title` model (or the `Titles` proxy) to *not* eager load custom field data.

#### With Categories

Eager load the `categories` attribute.

```
Entries::withCategories()->get();
```

#### With Category Fields

Eager load the `categories` attribute with custom category fields.

```
Entries::withCategoryFields()->get();
```

#### With Author

Eager load the `author` attribute.

```
Entries::withAuthor()->get();
```

#### With Author Fields

Eager load the `author` attribute with custom member fields.

```
Entries::withAuthorFields()->get();
```

#### With Parents

Eager load the `parents` attribute (native EE relationship fields only).

```
Entries::withParents()->get();
```

#### With Siblings

Eager load the `siblings` attribute (native EE relationship fields only).

```
Entries::withSiblings()->get();
```

#### With Comments

Eager load the `comments` attribute, a collection of Comment models.

```
Entries::withComments()->get();
```

### Custom Field Scopes

This set of scopes allows you to use the traditional some Eloquent methods with custom field names instead of `field_id_X`.

#### Order By Field

```
Entries::orderByField('your_custom_field', 'asc')->get();
```

#### Where Field

```
Entries::whereField('your_custom_field', 'foo')->get();
```

#### Or Where Field

```
Entries::orWhereField('your_custom_field', 'foo')->get();
```

#### Where Field In

```
Entries::whereFieldIn('your_custom_field', array('foo', 'bar'))->get();
```

#### Or Where Field In

```
Entries::orWhereFieldIn('your_custom_field', array('foo', 'bar'))->get();
```

#### Where Field Not In

```
Entries::whereFieldNotIn('your_custom_field', array('foo', 'bar'))->get();
```

#### Or Where Field Not In

```
Entries::orWhereFieldNotIn('your_custom_field', array('foo', 'bar'))->get();
```

#### Where Field Between

```
Entries::whereFieldBetween('your_custom_field', array(1, 10))->get();
```

#### Or Where Field Between

```
Entries::orWhereFieldBetween('your_custom_field', array(1, 10))->get();
```

#### Where Field Not Between

```
Entries::whereFieldNotBetween('your_custom_field', array(1, 10))->get();
```

#### Or Where Field Not Between

```
Entries::orWhereFieldNotBetween('your_custom_field', array(1, 10))->get();
```

#### Where Field Null

```
Entries::whereFieldNull('your_custom_field')->get();
```

#### Or Where Field Null

```
Entries::orWhereFieldNull('your_custom_field')->get();
```

#### Where Field Not Null

```
Entries::whereFieldNotNull('your_custom_field')->get();
```

#### Or Where Field Not Null

```
Entries::orWhereFieldNotNull('your_custom_field')->get();
```

#### Where Field Contains

This is like `search:your_custom_field="foo|bar"`.

```
Entries::whereFieldContains('your_custom_field', 'foo', 'bar')->get();
```

#### Or Where Field Contains

```
Entries::orWhereFieldContains('your_custom_field', 'foo', 'bar')->get();
```

#### Where Field Does Not Contain

This is like `search:your_custom_field="not foo|bar"`.

```
Entries::whereFieldDoesNotContain('your_custom_field', 'foo', 'bar')->get();
```

#### Or Where Field Does Not Contain

```
Entries::orWhereFieldDoesNotContain('your_custom_field', 'foo', 'bar')->get();
```

#### Where Field Contains Whole Word

This is like `search:your_custom_field="foo\W|bar\W"`.

```
Entries::whereFieldContainsWholeWord('your_custom_field', 'foo', 'bar')->get();
```

#### Or Where Field Contains Whole Word

```
Entries::orWhereFieldContainsWholeWord('your_custom_field', 'foo', 'bar')->get();
```

#### Where Field Does Not Contain Whole Word

This is like `search:your_custom_field="not foo\W|bar\W"`.

```
Entries::whereFieldDoesNotContainWholeWord('your_custom_field', 'foo', 'bar')->get();
```

#### Or Where Field Does Not Contain Whole Word

```
Entries::orWhereFieldDoesNotContainWholeWord('your_custom_field', 'foo', 'bar')->get();
```

### Advanced Category Querying

This library makes use of Eloquent's [relationship capabilities](http://laravel.com/docs/eloquent#querying-relations). If you need to do more advanced category querying than the default category scopes, you can use the `whereHas` and `orWhereHas` methods.

```
Entries::whereHas('categories', function ($query) {
    // category starts with A
    $query->where('cat_name', 'LIKE', 'A%');
})->get();
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
$entry->page_uri
```

### Dates

Entries have the following date properties. Each of these will be a [`Carbon` object](https://github.com/briannesbitt/Carbon). `expiration_date`, `comment_expiration_date` and `recent_comment_date` can be `null`.

```
$entry->entry_date
$entry->edit_date
$entry->expiration_date
$entry->comment_expiration_date
$entry->recent_comment_date
```

Dates are serialized to ISO-8601 format during toArray and toJson. To do this, Deep sets Carbon's default format to `DateTime::ISO8601` or `Y-m-d\TH:i:sO`. If you wish to change the default format, you should call `\Carbon\Carbon::setToStringFormat($yourDateFormatString)` prior to serialization. If you wish to reset this attribute globally in Carbon to the original default, you should call `Carbon::resetToStringFormat()`.

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

### Categories

Each `Entry` object has a `categories` property which is a collection of `Category` objects. Use the `withCategories` or `withCategoryFields` scope to eager load this relationship.

```
foreach ($entry->categories as $category) {
    echo '<li><a href="/blog/category/'.$category->cat_url_title.'">'.$category->cat_name.'</a></li>';
}
```

```
$category->cat_id
$category->site_id
$category->group_id
$category->parent_id
$category->cat_name
$category->cat_description
$category->cat_image
$category->cat_order
$category->your_custom_field
```

### Author

Each `Entry` object has a `author` property which is a `Member` object. Use the `withAuthor` or `withAuthorFields` scope to eager load this relationship.

```
$entry->author->member_id
$entry->author->group_id
$entry->author->username
$entry->author->screen_name
$entry->author->password
$entry->author->salt
$entry->author->unique_id
$entry->author->crypt_key
$entry->author->authcode
$entry->author->email
$entry->author->url
$entry->author->location
$entry->author->occupation
$entry->author->interests
$entry->author->bday_d
$entry->author->bday_m
$entry->author->bday_y
$entry->author->aol_im
$entry->author->yahoo_im
$entry->author->msn_im
$entry->author->icq
$entry->author->bio
$entry->author->signature
$entry->author->avatar_filename
$entry->author->avatar_width
$entry->author->avatar_height
$entry->author->photo_filename
$entry->author->photo_width
$entry->author->photo_height
$entry->author->sig_img_filename
$entry->author->sig_img_width
$entry->author->sig_img_height
$entry->author->ignore_list
$entry->author->private_messages
$entry->author->accept_messages
$entry->author->last_view_bulletins
$entry->author->last_bulletin_date
$entry->author->ip_address
$entry->author->join_date
$entry->author->last_visit
$entry->author->last_activity
$entry->author->total_entries
$entry->author->total_comments
$entry->author->total_forum_topics
$entry->author->total_forum_posts
$entry->author->last_entry_date
$entry->author->last_comment_date
$entry->author->last_forum_post_date
$entry->author->last_email_date
$entry->author->in_authorlist
$entry->author->accept_admin_email
$entry->author->accept_user_email
$entry->author->notify_by_default
$entry->author->notify_of_pm
$entry->author->display_avatars
$entry->author->display_signatures
$entry->author->parse_smileys
$entry->author->smart_notifications
$entry->author->language
$entry->author->timezone
$entry->author->time_format
$entry->author->include_seconds
$entry->author->date_format
$entry->author->cp_theme
$entry->author->profile_theme
$entry->author->forum_theme
$entry->author->tracker
$entry->author->template_size
$entry->author->notepad
$entry->author->notepad_size
$entry->author->quick_links
$entry->author->quick_tabs
$entry->author->show_sidebar
$entry->author->pmember_id
$entry->author->rte_enabled
$entry->author->rte_toolset_id
$entry->author->your_custom_field
```

### Comments

Each `Entry` object has a `comments` property which is a collection of `Comment` objects. Use the `withComments` scope to eager load this relationship.

```
$entry->comment->comment_id
$entry->comment->site_id
$entry->comment->entry_id
$entry->comment->channel_id
$entry->comment->author_id
$entry->comment->status
$entry->comment->name
$entry->comment->email
$entry->comment->url
$entry->comment->location
$entry->comment->ip_address
$entry->comment->comment_date
$entry->comment->edit_date
$entry->comment->comment
$entry->comment->author->member_id
$entry->comment->author->username
$entry->comment->author->screen_name
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

### Multiselect, Checkboxes, Fieldpack Multiselect, Fieldpack Checkboxes & Fieldpack List

These fields will be arrays of values:

```
foreach ($entry->your_multiselect_field as $value) {
    echo $value;
}
```

## Extending the `BasePlugin` class

The abstract `rsanchez\Deep\Plugin\BasePlugin` class is provided as a base for ExpressionEngine modules and plugins. The `parseEntries` method parses a template using an `EntryCollection`.

### Entries

```
<?php

use rsanchez\Deep\Plugin\BasePlugin;

class My_plugin extends BasePlugin
{
    public function entries()
    {
        return $this->parseEntries();
    }

    public function entries_that_start_with()
    {
        $letter = ee()->TMPL->fetch_param('letter');

        return $this->parseEntries(function ($query) use ($letter) {
            // do additional custom querying here
            $query->where('title', 'LIKE', $letter.'%');
        });
    }
}

```

Now you can parse your plugin like a channel:entries tag:

```
{exp:my_plugin:entries channel="blog"}
  {title}
  {url_title_path="blog/view"}
{/exp:my_plugin:entries}

{exp:my_plugin:entries_that_start_with channel="blog" letter="A"}
  {title}
  {url_title_path="blog/view"}
{/exp:my_plugin:entries_that_start_with}
```

The following channel:entries single tags / conditionals are not implemented by the `BasePlugin` class:

- gmt_entry_date
- gmt_edit_date
- member_search_path
- relative_url
- relative_date
- trimmed_url
- week_date

The following channel:entries parameters are not implemented by the `BasePlugin` class:

- display_by
- dynamic_start
- month_limit
- paginate_type
- relaxed_categories
- show_current_week
- track_views
- week_sort
- uncategorized_entries

The `parseEntries` method has the following default parameters:

    orderby="entry_date"
    show_future_entries="no"
    show_expired="no"
    sort="desc"
    status="open"
    dynamic="yes"
    limit="100"

You can change this by overloading the `getEntriesDefaultParameters` method in your plugin/module class:

    protected function getEntriesDefaultParameters()
    {
        return array(
            'dynamic' => 'no',
            'status' => 'open|Featured',
        );
    }

The `BasePlugin` class allows the following parameters on Matrix, Grid, Playa and Relationships tag pairs:

- limit
- offset
- orderby
- sort
- search:your_field
- fixed_order
- backspace
- entry_id\*
- row_id\*\*

\*Playa and Relationships only
\*\*Matrix and Grid only

### Categories

The `BasePlugin` class can also parse the equivalent of a channel:categories tag.

```
<?php

use rsanchez\Deep\Plugin\BasePlugin;

class My_plugin extends BasePlugin
{
    public function categories()
    {
        return $this->parseCategories();
    }

    public function offices()
    {
        $country = ee()->TMPL->fetch_param('country', 'us');

        ee()->TMPL->tagparams['style'] = 'linear';

        return $this->parseCategories(function ($query) use ($country) {
            return $query->channel('offices')->where('categories.cat_name', $country);
        });
    }
}

```

Now you can parse your plugin like a channel:categories tag:

```
{exp:my_plugin:categories channel="blog" style="nested"}
  <a href="{path='blog'}"{if active} class="active"{/if}>{category_name}</a>
{/exp:my_plugin:categories}

{exp:my_plugin:offices country="{segment_2}"}
{if no_results}{redirect="404"}{/if}
<h1><a href="{site_url}offices/{category_url_title}">{category_name}</a></h1>
{category_description}
{/exp:my_plugin:offices}
```

The `parseCategories` method has the following default parameters:

    show_empty="yes"
    show_future_entries="no"
    show_expired="no"
    restrict_channel="yes"
    style="nested"
    id="nav_categories"
    class="nav_categories"
    orderby="categories.group_id|categories.parent_id|categories.cat_order"

You can change this by overloading the `getCategoriesDefaultParameters` method in your plugin/module class:

    protected function getCategoriesDefaultParameters()
    {
        $params = parent::getCategoriesDefaultParameters();

        $params['style'] = 'linear';

        return $params;
    }

## The `Titles` Class

You might be wondering how to do the equivalent of `disable="custom_fields"`. You can use the `Titles` class for this, which will not query for custom fields.

NOTE: The `Title` model does NOT implement the [Search](#search) and [Custom Field](#custom-field-scopes) scopes.

```
<?php

use rsanchez\Deep\App\EE\Titles;

$entries = Titles::channel('blog')
                ->limit(1)
                ->get();
```

Note: Use `rsanchez\Deep\App\Titles` (or use the Service Provider in Laravel) *outside* of an EE context.
