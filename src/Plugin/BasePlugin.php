<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Plugin;

use rsanchez\Deep\Deep;
use rsanchez\Deep\Model\Entry;
use rsanchez\Deep\Model\Category;
use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Collection\RelationshipCollection;
use rsanchez\Deep\Collection\FilterableInterface;
use rsanchez\Deep\Collection\CategoryCollection;
use Illuminate\Support\Collection;
use DateTime;
use Closure;

/**
 * Base class for EE modules/plugins
 */
abstract class BasePlugin
{
    /**
     * @var \rsanchez\Deep\Container
     */
    protected $app;

    /**
     * @var \Pagination_object
     */
    protected $paginator;

    /**
     * Constructor
     * @return void
     */
    public function __construct()
    {
        $this->app = Deep::getInstance();

        $this->app->bootEE(ee());

        ee()->load->library(['pagination', 'typography']);
    }

    /**
     * Get the default tag parameters when using parseEntries
     *
     * @return array
     */
    protected function getEntriesDefaultParameters()
    {
        return [
            'dynamic' => 'yes',
            'limit' => '100',
            'orderby' => 'entry_date',
            'paginate' => 'bottom',
            'show_future_entries' => 'no',
            'show_expired' => 'no',
            'sort' => 'desc',
            'status' => 'open',
        ];
    }

    /**
     * Get the default tag parameters when using parseEntries
     *
     * @return array
     */
    protected function getCategoriesDefaultParameters()
    {
        return [
            'show_empty' => 'yes',
            'show_future_entries' => 'no',
            'show_expired' => 'no',
            'restrict_channel' => 'yes',
            'style' => 'nested',
            'id' => 'nav_categories',
            'orderby' => 'categories.group_id|categories.parent_id|categories.cat_order',
        ];
    }

    /**
     * Get an EntryCollection based on the state of ee()->TMPL
     *
     * @param  Closure|null                              $callback receieves a query builder object as the first parameter
     * @return \rsanchez\Deep\Collection\EntryCollection
     */
    protected function getEntries(Closure $callback = null)
    {
        foreach ($this->getEntriesDefaultParameters() as $key => $value) {
            if (! isset(ee()->TMPL->tagparams[$key])) {
                ee()->TMPL->tagparams[$key] = $value;
            }
        }

        $disabled = empty(ee()->TMPL->tagparams['disable']) ? [] : explode('|', ee()->TMPL->tagparams['disable']);

        $this->paginator = ee()->pagination->create();

        $limit = ee()->TMPL->fetch_param('limit');

        ee()->TMPL->tagdata = $this->paginator->prepare(ee()->TMPL->tagdata);

        $customFieldsEnabled = ! in_array('custom_fields', $disabled);
        $memberDataEnabled = ! in_array('members', $disabled);
        $paginationEnabled = ! in_array('pagination', $disabled);
        $categoriesEnabled = ! in_array('categories', $disabled);
        $categoryFieldsEnabled = $categoriesEnabled && ! in_array('category_fields', $disabled);

        if ($limit && $paginationEnabled) {
            unset(ee()->TMPL->tagparams['offset']);
        } else {
            $this->paginator->paginate = false;
        }

        $uri = ee()->uri->page_query_string ?: ee()->uri->query_string;

        if (preg_match('#^((.*?)/)?P\d+/?$#', $uri, $match)) {
            $uri = $match[2];
        }

        if (! $uri && ee()->TMPL->fetch_param('require_entry') === 'yes') {
            return ee()->TMPL->no_results();
        }

        ee()->TMPL->tagparams['category_request'] = false;

        $singleEntry = false;

        $relatedCategoriesMode = ee()->TMPL->fetch_param('related_categories_mode') === 'yes';

        $dynamic = ee()->TMPL->fetch_param('dynamic', 'yes') === 'yes';

        $query = $this->app->make('Entry')->newQuery();

        if (! $customFieldsEnabled) {
            $query->withoutFields();
        }

        if ($uri && ($dynamic || $relatedCategoriesMode)) {
            $segments = explode('/', $uri);
            $lastSegment = array_pop($segments);
            $penultimateSegment = array_pop($segments);

            if (preg_match('#(^|/)(\d{4})/(\d{2})(/(\d{2}))?/?$#', $uri, $match)) {
                ee()->TMPL->tagparams['year'] = $match[2];
                ee()->TMPL->tagparams['month'] = $match[3];

                if (isset($match[5])) {
                    ee()->TMPL->tagparams['day'] = $match[5];
                }
            } elseif (is_numeric($lastSegment)) {
                if ($relatedCategoriesMode) {
                    $singleEntry = true;
                    $query->relatedCategories($lastSegment);
                    ee()->TMPL->tagparams['dynamic'] = 'no';
                } else {
                    ee()->TMPL->tagparams['entry_id'] = $lastSegment;
                }
            } elseif (
                ee()->config->item('use_category_name') === 'y' &&
                $penultimateSegment === ee()->config->item('reserved_category_word')
            ) {
                ee()->TMPL->tagparams['category_name'] = $lastSegment;

                ee()->TMPL->tagparams['category_request'] = true;
            } elseif (
                ee()->config->item('use_category_name') !== 'y' &&
                preg_match('#(^|/)C(\d+)#', $uri, $match)
            ) {
                ee()->TMPL->tagparams['category'] = $match[2];

                ee()->TMPL->tagparams['category_request'] = true;
            } else {
                if ($relatedCategoriesMode) {
                    $singleEntry = true;
                    $query->relatedCategoriesUrlTitle($lastSegment);
                    ee()->TMPL->tagparams['dynamic'] = 'no';
                } else {
                    ee()->TMPL->tagparams['url_title'] = $lastSegment;
                }
            }
        }

        if ($relatedCategoriesMode && ! $singleEntry) {
            return ee()->TMPL->no_results();
        }

        $query->tagparams(ee()->TMPL->tagparams);

        if ($categoriesEnabled) {
            $query->withCategories(function ($query) use ($categoryFieldsEnabled) {
                if ($categoryFieldsEnabled) {
                    $query->withFields();
                }

                return $query->orderBy('categories.group_id')
                    ->orderBy('categories.parent_id')
                    ->orderBy('categories.cat_order');
            });
        }

        if ($memberDataEnabled) {
            $query->withAuthorFields();
        }

        $connection = $query->getQuery()->getConnection();
        $tablePrefix = $connection->getTablePrefix();

        if (strpos(ee()->TMPL->tagdata, 'comment_subscriber_total') !== false) {
            $subquery = "(select count(*) "
                ."from `{$tablePrefix}comment_subscriptions` "
                ."where `{$tablePrefix}comment_subscriptions`.`entry_id` "
                ."= `{$tablePrefix}`channel_titles`.`entry_id`) "
                ."as `comment_subscriber_total`";

            $query->addSelect($connection->raw($subquery));
        }

        $prefix = ee()->TMPL->fetch_param('var_prefix') ? ee()->TMPL->fetch_param('var_prefix').':' : '';

        if (strpos(ee()->TMPL->tagdata, '{'.$prefix.'parents') !== false) {
            $query->withParents();
        }

        if (strpos(ee()->TMPL->tagdata, '{'.$prefix.'siblings') !== false) {
            $query->withSiblings();
        }

        if (is_callable($callback)) {
            $callback($query);
        }

        ee()->TMPL->tagparams['absolute_results'] = $limit;

        if ($this->paginator->paginate) {
            ee()->TMPL->tagparams['absolute_results'] = $query->getQuery()->getPaginationCount();

            $this->paginator->build(ee()->TMPL->tagparams['absolute_results'], $limit);

            if ($this->paginator->offset) {
                $query->skip($this->paginator->offset);

                ee()->TMPL->tagparams['offset'] = $this->paginator->offset;
            }
        }

        return $query->get();
    }

    /**
     * Parse a plugin tag pair equivalent to channel:entries
     *
     * @param  Closure|null $callback receieves a query builder object as the first parameter
     * @return string
     */
    public function parseEntries(Closure $callback = null)
    {
        $entries = $this->getEntries($callback);

        $output = $this->parseEntryCollection(
            $entries,
            ee()->TMPL->tagdata,
            ee()->TMPL->tagparams,
            ee()->TMPL->var_pair,
            ee()->TMPL->var_single
        );

        if ($this->paginator->paginate) {
            $output = $this->paginator->render($output);
        }

        return $output;
    }

    /**
     * Parse a plugin tag pair equivalent to channel:entries
     *
     * @param  EntryCollection $entries   a collection of entries
     * @param  string          $tagdata   the raw template to parse
     * @param  array           $params    channel:entries parameters
     * @param  array           $varPair   array of pair tags from ee()->functions->assign_variables
     * @param  array           $varSingle array single tags from ee()->functions->assign_variables
     * @return string
     */
    protected function parseEntryCollection(
        EntryCollection $entries,
        $tagdata,
        array $params = [],
        array $varPair = [],
        array $varSingle = []
    ) {
        $disabled = empty($params['disable']) ? [] : explode('|', $disable);

        $offset = isset($params['offset']) ? $params['offset'] : 0;

        $absoluteResults = isset($params['absolute_results']) ? $params['absolute_results'] : $entries->count();

        $customFieldsEnabled = ! in_array('custom_fields', $disabled);
        $memberDataEnabled = ! in_array('member_data', $disabled);
        $categoriesEnabled = ! in_array('categories', $disabled);
        $categoryFieldsEnabled = $categoriesEnabled && ! in_array('category_fields', $disabled);

        ee()->load->library('typography');

        if (! empty($params['var_prefix'])) {
            $prefix = rtrim($params['var_prefix'], ':').':';
            $prefixLength = strlen($prefix);
        } else {
            $prefix = '';
            $prefixLength = 0;
        }

        $singleTags = [];
        $pairTags = [];

        foreach (array_keys($varSingle) as $tag) {
            $spacePosition = strpos($tag, ' ');

            if ($spacePosition !== false) {
                $name = substr($tag, 0, $spacePosition);
                $tagparams = ee()->functions->assign_parameters(substr($tag, $spacePosition));
            } elseif (preg_match('#^([a-z_]+)=([\042\047]?)?(.*?)\\2$#', $tag, $match)) {
                $name = $match[1];
                $tagparams = $match[3] ? [$match[3]] : [''];
            } else {
                $name = $tag;
                $tagparams = [];
            }

            if ($prefix && strncmp($name, $prefix, $prefixLength) !== 0) {
                continue;
            }

            $singleTags[] = (object) [
                'name' => $prefix ? substr($name, $prefixLength) : $name,
                'key' => $tag,
                'params' => $tagparams,
                'tagdata' => '',
            ];
        }

        foreach ($varPair as $tag => $tagparams) {
            $spacePosition = strpos($tag, ' ');

            $name = $spacePosition === false ? $tag : substr($tag, 0, $spacePosition);

            if ($prefix && strncmp($name, $prefix, $prefixLength) !== 0) {
                continue;
            }

            preg_match_all('#{('.preg_quote($tag).'}(.*?){/'.preg_quote($name).')}#s', $tagdata, $matches);

            foreach ($matches[1] as $i => $key) {
                $pairTags[] = (object) [
                    'name' => $prefix ? substr($name, $prefixLength) : $name,
                    'key' => $key,
                    'params' => $tagparams ?: [],
                    'tagdata' => $matches[2][$i],
                ];
            }
        }

        $variables = [];

        foreach ($entries as $i => $entry) {
            $row = [
                $prefix.'absolute_count' => $offset + $i + 1,
                $prefix.'absolute_results' => $absoluteResults,
                $prefix.'category_request' => isset($params['category_request']) ? $params['category_request'] : false,
                $prefix.'not_category_request' => isset($params['category_request']) ? ! $params['category_request'] : true,
                $prefix.'channel' => $entry->channel->channel_name,
                $prefix.'channel_short_name' => $entry->channel->channel_name,
                $prefix.'comment_auto_path' => $entry->channel->comment_url,
                $prefix.'comment_entry_id_auto_path' => $entry->channel->comment_url.'/'.$entry->entry_id,
                $prefix.'comment_url_title_auto_path' => $entry->channel->comment_url.'/'.$entry->url_title,
                $prefix.'entry_site_id' => $entry->site_id,
                $prefix.'forum_topic' => (int) (bool) $entry->forum_topic_id,
                $prefix.'not_forum_topic' => (int) ! $entry->forum_topic_id,
                $prefix.'page_uri' => $entry->page_uri,
                $prefix.'page_url' => ee()->functions->create_url($entry->page_uri),
                $prefix.'entry_id_path' => [$entry->entry_id, ['path_variable' => true]],
                $prefix.'permalink' => [$entry->entry_id, ['path_variable' => true]],
                $prefix.'title_permalink' => [$entry->url_title, ['path_variable' => true]],
                $prefix.'url_title_path' => [$entry->url_title, ['path_variable' => true]],
                $prefix.'profile_path' => [$entry->author_id, ['path_variable' => true]],
            ];

            foreach ($pairTags as $tag) {
                if ($tag->name === 'parents' || $tag->name === 'siblings') {
                    $value = $entry->{$tag->name};

                    $tag->params['var_prefix'] = $prefix.$tag->name;
                    $tag->vars = ee()->functions->assign_variables($tag->tagdata);

                    if (! empty($tag->params['field'])) {
                        $fieldRepository = $this->app->make('FieldRepository');

                        $fieldIds = [];

                        foreach (explode('|', $tag->params['field']) as $fieldName) {
                            if ($fieldRepository->hasField($fieldName)) {
                                $fieldIds[] = $fieldRepository->getFieldId($fieldName);
                            }
                        }

                        if ($fieldIds) {
                            $tag->params['field_id'] = implode('|', $fieldIds);
                        }

                        unset($tag->params['field']);
                    }

                    $row[$tag->key] = $this->parseEntryCollection(
                        $value($tag->params),
                        $tag->tagdata,
                        $tag->params,
                        $tag->vars['var_pair'],
                        $tag->vars['var_single']
                    );
                } elseif ($categoriesEnabled && $tag->name === 'categories') {
                    $categories = [];

                    foreach ($entry->categories->tagparams($tag->params) as $categoryModel) {
                        $category = $categoryModel->toArray();

                        unset(
                            $category['cat_id'],
                            $category['cat_name'],
                            $category['cat_description'],
                            $category['cat_image'],
                            $category['cat_url_title'],
                            $category['group_id']
                        );

                        $categoryUri = ee()->config->item('use_category_name') === 'y'
                            ? '/'.ee()->config->item('reserved_category_word').'/'.$categoryModel->cat_url_title
                            : '/C'.$categoryModel->cat_id;

                        $regex = '#'.preg_quote($categoryUri).'(\/|\/P\d+\/?)?$#';

                        $category['active'] = (bool) preg_match($regex, ee()->uri->uri_string());
                        $category['category_description'] = $categoryModel->cat_description;
                        $category['category_group'] = $categoryModel->group_id;
                        $category['category_id'] = $categoryModel->cat_id;
                        $category['category_image'] = $categoryModel->cat_image;
                        $category['category_name'] = $categoryModel->cat_name;
                        $category['category_url_title'] = $categoryModel->cat_url_title;
                        $category['path'] = [$categoryUri, ['path_variable' => true]];

                        array_push($categories, $category);
                    }

                    // @TODO parse the file path at the model attribute level using upload pref repository
                    $row[$tag->key] = $categories ? ee()->typography->parse_file_paths(ee()->TMPL->parse_variables($tag->tagdata, $categories)) : '';
                } elseif ($customFieldsEnabled && $entry->channel->fields->hasField($tag->name)) {
                    $row[$tag->key] = '';

                    $value = $entry->{$tag->name};

                    if ($value instanceof EntryCollection) {
                        // native relationships are prefixed by default
                        if ($value instanceof RelationshipCollection) {
                            $tag->params['var_prefix'] = $tag->name;
                        }

                        $tag->vars = ee()->functions->assign_variables($tag->tagdata);

                        $value = $this->parseEntryCollection(
                            $value($tag->params),
                            $tag->tagdata,
                            $tag->params,
                            $tag->vars['var_pair'],
                            $tag->vars['var_single']
                        );
                    } elseif ($value instanceof FilterableInterface) {
                        $value = $value($tag->params)->toArray();
                    } elseif (is_object($value) && method_exists($value, 'toArray')) {
                        $value = $value->toArray();
                    } elseif ($value) {
                        $value = (string) $value;
                    }

                    if ($value) {
                        if (is_array($value)) {
                            $row[$tag->key] = ee()->TMPL->parse_variables($tag->tagdata, $value);

                            if (isset($tag->params['backspace'])) {
                                $row[$tag->key] = substr($row[$tag->key], 0, -$tag->params['backspace']);
                            }
                        } else {
                            $row[$tag->key] = $value;
                        }
                    }
                }
            }

            foreach ($singleTags as $tag) {
                if ($customFieldsEnabled && $entry->channel->fields->hasField($tag->name)) {
                    $row[$tag->key] = (string) $entry->{$tag->name};
                }

                if ($entry->{$tag->name} instanceof DateTime) {
                    $format = isset($tag->params['format']) ? preg_replace('#%([a-zA-Z])#', '\\1', $tag->params['format']) : 'U';

                    $row[$tag->key] = $entry->{$tag->name}->format($format);
                }
            }

            foreach ($entry->getOriginal() as $key => $value) {
                $row[$prefix.$key] = $value;
            }

            foreach ($entry->channel->toArray() as $key => $value) {
                $row[$prefix.$key] = $value;
            }

            $row[$prefix.'allow_comments'] = (int) ($entry->allow_comments === 'y');
            $row[$prefix.'sticky'] = (int) ($entry->sticky === 'y');

            if ($memberDataEnabled) {
                foreach ($entry->author->toArray() as $key => $value) {
                    $row[$prefix.$key] = $value;
                }

                $row[$prefix.'author'] = $entry->author->screen_name ?: $entry->author->username;
                $row[$prefix.'avatar_url'] = $entry->author->avatar_filename ? ee()->config->item('avatar_url').$entry->author->avatar_filename : '';
                $row[$prefix.'avatar_image_height'] = $entry->author->avatar_height;
                $row[$prefix.'avatar_image_width'] = $entry->author->avatar_width;
                $row[$prefix.'avatar'] = (int) (bool) $entry->author->avatar_filename;
                $row[$prefix.'photo_url'] = $entry->author->photo_filename ? ee()->config->item('photo_url').$entry->author->photo_filename : '';
                $row[$prefix.'photo_image_height'] = $entry->author->photo_height;
                $row[$prefix.'photo_image_width'] = $entry->author->photo_width;
                $row[$prefix.'photo'] = (int) (bool) $entry->author->photo_filename;
                $row[$prefix.'signature_image_url'] = $entry->author->sig_img_filename ? ee()->config->item('sig_img_url').$entry->author->sig_img_filename : '';
                $row[$prefix.'signature_image_height'] = $entry->author->sig_img_height;
                $row[$prefix.'signature_image_width'] = $entry->author->sig_img_width;
                $row[$prefix.'signature_image'] = (int) (bool) $entry->author->sig_img_filename;
                $row[$prefix.'url_or_email'] = $entry->author->url ?: $entry->author->email;
                $row[$prefix.'url_or_email_as_author'] = '<a href="'.($entry->author->url ?: 'mailto:'.$entry->author->email).'">'.$row[$prefix.'author'].'</a>';
                $row[$prefix.'url_or_email_as_link'] = '<a href="'.($entry->author->url ?: 'mailto:'.$entry->author->email).'">'.$row[$prefix.'url_or_email'].'</a>';
            }

            $variables[] = $row;
        }

        if (preg_match('#{if '.preg_quote($prefix).'no_results}(.*?){/if}#s', $tagdata, $match)) {
            $tagdata = str_replace($match[0], '', $tagdata);
            ee()->TMPL->no_results = $match[1];
        }

        if (! $variables) {
            return ee()->TMPL->no_results();
        }

        $output = ee()->TMPL->parse_variables($tagdata, $variables);

        if (! empty($params['backspace'])) {
            $output = substr($output, 0, -$params['backspace']);
        }

        return $output;
    }

    /**
     * Parse a plugin tag pair equivalent to channel:categories
     *
     * @param  Closure|null $callback receieves a query builder object as the first parameter
     * @return string
     */
    protected function parseCategories(Closure $callback = null)
    {
        foreach ($this->getCategoriesDefaultParameters() as $key => $value) {
            if (! isset(ee()->TMPL->tagparams[$key])) {
                ee()->TMPL->tagparams[$key] = $value;
            }
        }

        $query = $this->app->make('Category')->nested()->tagparams(ee()->TMPL->tagparams);

        $customFieldsEnabled = ee()->TMPL->fetch_param('disable') !== 'category_fields';

        if ($customFieldsEnabled) {
            $query->withFields();
        }

        if (is_callable($callback)) {
            $callback($query);
        }

        $categories = $query->get();

        $prefix = ee()->TMPL->fetch_param('var_prefix') ? ee()->TMPL->fetch_param('var_prefix').':' : '';

        if (preg_match('#{if '.preg_quote($prefix).'no_results}(.*?){/if}#s', ee()->TMPL->tagdata, $match)) {
            ee()->TMPL->tagdata = str_replace($match[0], '', ee()->TMPL->tagdata);
            ee()->TMPL->no_results = $match[1];
        }

        if ($categories->isEmpty()) {
            return ee()->TMPL->no_results();
        }

        if (ee()->TMPL->fetch_param('style') === 'nested') {
            $output = '<ul id="'.ee()->TMPL->fetch_param('id', 'nav_categories').'" class="'.ee()->TMPL->fetch_param('class', 'nav_categories').'">';

            foreach ($categories as $category) {
                $output .= $this->categoryToList($category, ee()->TMPL->tagdata, $customFieldsEnabled, $prefix);
            }

            $output .= '</ul>';
        } else {
            $variables = [];

            $this->categoryCollectionToVariables($categories, $variables, $customFieldsEnabled, $prefix);

            $output = ee()->TMPL->parse_variables(ee()->TMPL->tagdata, $variables);

            if ($backspace = ee()->TMPL->fetch_param('backspace')) {
                $output = substr($output, 0, -$backspace);
            }
        }

        return $output;
    }

    /**
     * Convert a Category collection into a multi-dimensional array suitable for ee()->TMPL->parse_variables
     *
     * @param  CategoryCollection $categories
     * @param  array              $variables
     * @param  boolean            $customFieldsEnabled
     * @param  string             $prefix
     * @return void
     */
    protected function categoryCollectionToVariables(CategoryCollection $categories, array &$variables, $customFieldsEnabled = false, $prefix = '')
    {
        foreach ($categories as $category) {
            $variables[] = $this->categoryToVariables($category, $customFieldsEnabled, $prefix);

            if ($category->hasChildren()) {
                $this->categoryCollectionToVariables($category->children, $variables, $customFieldsEnabled, $prefix);
            }
        }
    }

    /**
     * Convert a Category model into an array suitable for ee()->TMPL->parse_variables_row
     *
     * @param  Category $category
     * @param  boolean  $customFieldsEnabled
     * @param  string   $prefix
     * @return array
     */
    protected function categoryToVariables(Category $category, $customFieldsEnabled = false, $prefix = '')
    {
        $categoryUri = ee()->config->item('use_category_name') === 'y'
            ? '/'.ee()->config->item('reserved_category_word').'/'.$category->cat_url_title
            : '/C'.$category->cat_id;

        $regex = '#'.preg_quote($categoryUri).'(\/|\/P\d+\/?)?$#';

        $row = [
            $prefix.'active' => (bool) preg_match($regex, ee()->uri->uri_string()),
            $prefix.'category_description' => $category->cat_description,
            $prefix.'category_id' => $category->cat_id,
            $prefix.'parent_id' => $category->parent_id,
            $prefix.'category_image' => $category->cat_image,
            $prefix.'category_name' => $category->cat_name,
            $prefix.'category_url_title' => $category->cat_url_title,
            $prefix.'path' => [$categoryUri, ['path_variable' => true]],
        ];

        if ($customFieldsEnabled) {
            foreach ($category->getFields() as $field) {
                $row[$prefix.$field->field_name] = $category->{$field->field_name};
            }
        }

        return $row;
    }

    /**
     * Convert a Category model into a list element
     *
     * @param  Category $category
     * @param  [type]   $tagdata
     * @param  boolean  $customFieldsEnabled
     * @param  string   $prefix
     * @return string
     */
    protected function categoryToList(Category $category, $tagdata, $customFieldsEnabled = false, $prefix = '')
    {
        $output = '<li>';

        $output .= ee()->TMPL->parse_variables_row($tagdata, $this->categoryToVariables($category, $customFieldsEnabled, $prefix));

        if ($category->hasChildren()) {
            $output .= ' <ul>';

            foreach ($category->children as $child) {
                $output .= $this->categoryToList($child, $tagdata, $customFieldsEnabled, $prefix);
            }

            $output .= '</ul>';
        }

        $output .= '</li>';

        return $output;
    }
}
