<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Plugin;

use rsanchez\Deep\Deep;
use rsanchez\Deep\App\Entries;
use rsanchez\Deep\Model\Entry;
use rsanchez\Deep\Collection\AbstractTitleCollection;
use rsanchez\Deep\Collection\RelationshipCollection;
use rsanchez\Deep\Collection\AbstractFilterableCollection;
use Illuminate\Support\Collection;
use DateTime;
use Closure;

/**
 * Base class for EE modules/plugins
 */
abstract class BasePlugin
{
    /**
     * Constructor
     * @return void
     */
    public function __construct()
    {
        Deep::extendInstance('config', function () {
            return ee()->config->config;
        });

        Deep::bootEloquent(ee());

        $this->app = Deep::getInstance();

        ee()->load->library(array('pagination', 'typography'));
    }

    /**
     * Get the default tag parameters when using parseEntries
     *
     * @return array
     */
    protected function getEntriesDefaultParameters()
    {
        return array(
            'dynamic' => 'yes',
            'limit' => '100',
            'orderby' => 'entry_date',
            'paginate' => 'bottom',
            'show_future_entries' => 'no',
            'show_expired' => 'no',
            'sort' => 'desc',
            'status' => 'open',
        );
    }

    /**
     * Parse a plugin tag pair equivalent to channel:entries
     *
     * @param  Closure|null $callback receieves a query builder object as the first parameter
     * @return string
     */
    protected function parseEntries(Closure $callback = null)
    {
        foreach ($this->getEntriesDefaultParameters() as $key => $value) {
            if (! isset(ee()->TMPL->tagparams[$key])) {
                ee()->TMPL->tagparams[$key] = $value;
            }
        }

        $disabled = empty(ee()->TMPL->tagparams['disable']) ? array() : explode('|', ee()->TMPL->tagparams['disable']);

        $pagination = ee()->pagination->create();

        $limit = ee()->TMPL->fetch_param('limit');

        ee()->TMPL->tagdata = $pagination->prepare(ee()->TMPL->tagdata);

        $customFieldsEnabled = ! in_array('custom_fields', $disabled);
        $memberDataEnabled = ! in_array('members', $disabled);
        $paginationEnabled = ! in_array('pagination', $disabled);
        $categoriesEnabled = ! in_array('categories', $disabled);
        $categoryFieldsEnabled = $categoriesEnabled && ! in_array('category_fields', $disabled);

        if ($limit && $paginationEnabled) {
            unset(ee()->TMPL->tagparams['offset']);
        } else {
            $pagination->paginate = false;
        }

        $builderClass = $customFieldsEnabled ? 'Entries' : 'Titles';

        if ($hasPaginationOffset = preg_match('#^((.*?)/)?P(\d+)/?$#', ee()->uri->uri_string(), $match)) {
            $paginationOffset = $match[3];
        }

        $query = call_user_func(array('\\rsanchez\\Deep\\App\\'.$builderClass, 'tagparams'), ee()->TMPL->tagparams);

        if ($categoriesEnabled) {
            $query->withCategories($categoryFieldsEnabled);
        }

        if ($memberDataEnabled) {
            $query->withAuthor(true);
        }

        $connection = $query->getQuery()->getConnection();
        $tablePrefix = $connection->getTablePrefix();

        if (strpos(ee()->TMPL->tagdata, 'comment_subscriber_total') !== false) {
            $subquery = "(SELECT COUNT(*)
                FROM {$tablePrefix}comment_subscriptions
                WHERE {$tablePrefix}comment_subscriptions.entry_id = {$tablePrefix}channel_titles.entry_id)
                AS comment_subscriber_total";

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

        if ($pagination->paginate) {
            ee()->TMPL->tagparams['absolute_results'] = $query->getQuery()->getPaginationCount();

            if ($hasPaginationOffset) {
                $query->skip($paginationOffset);

                ee()->TMPL->tagparams['offset'] = $paginationOffset;
            }

            $pagination->build(ee()->TMPL->tagparams['absolute_results'], $limit);
        }

        $output = $this->parseEntryCollection(
            $query->get(),
            ee()->TMPL->tagdata,
            ee()->TMPL->tagparams,
            ee()->TMPL->var_pair,
            ee()->TMPL->var_single
        );

        if ($pagination->paginate) {
            $output = $pagination->render($output);
        }

        return $output;
    }

    /**
     * Parse a plugin tag pair equivalent to channel:entries
     *
     * @param  AbstractTitleCollection $entries   a collection of entries
     * @param  string                  $tagdata   the raw template to parse
     * @param  array                   $params    channel:entries parameters
     * @param  array                   $varPair   array of pair tags from ee()->functions->assign_variables
     * @param  array                   $varSingle array single tags from ee()->functions->assign_variables
     * @return string
     */
    protected function parseEntryCollection(
        AbstractTitleCollection $entries,
        $tagdata,
        array $params = array(),
        array $varPair = array(),
        array $varSingle = array()
    ) {
        $disabled = empty($params['disable']) ? array() : explode('|', $disable);

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

        $singleTags = array();
        $pairTags = array();

        foreach (array_keys($varSingle) as $tag) {
            $spacePosition = strpos($tag, ' ');

            if ($spacePosition !== false) {
                $name = substr($tag, 0, $spacePosition);
                $params = ee()->functions->assign_parameters(substr($tag, $spacePosition));
            } elseif (preg_match('#^([a-z_]+)=([\042\047]?)?(.*?)\\2$#', $tag, $match)) {
                $name = $match[1];
                $params = $match[3] ? array($match[3]) : array('');
            } else {
                $name = $tag;
                $params = array();
            }

            if ($prefix && strncmp($name, $prefix, $prefixLength) !== 0) {
                continue;
            }

            $singleTags[] = (object) array(
                'name' => $prefix ? substr($name, $prefixLength) : $name,
                'key' => $tag,
                'params' => $params,
                'tagdata' => '',
            );
        }

        foreach ($varPair as $tag => $params) {
            $spacePosition = strpos($tag, ' ');

            $name = $spacePosition === false ? $tag : substr($tag, 0, $spacePosition);

            if ($prefix && strncmp($name, $prefix, $prefixLength) !== 0) {
                continue;
            }

            preg_match_all('#{('.preg_quote($tag).'}(.*?){/'.preg_quote($name).')}#s', $tagdata, $matches);

            foreach ($matches[1] as $i => $key) {
                $pairTags[] = (object) array(
                    'name' => $prefix ? substr($name, $prefixLength) : $name,
                    'key' => $key,
                    'params' => $params ?: array(),
                    'tagdata' => $matches[2][$i],
                );
            }
        }

        $variables = array();

        foreach ($entries as $i => $entry) {
            $row = array(
                $prefix.'absolute_count' => $offset + $i + 1,
                $prefix.'absolute_results' => $absoluteResults,
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
            );

            foreach ($pairTags as $tag) {
                if ($tag->name === 'parents' || $tag->name === 'siblings') {
                    $value = $entry->{$tag->name};

                    $tag->params['var_prefix'] = $prefix.$tag->name;
                    $tag->vars = ee()->functions->assign_variables($tag->tagdata);

                    if (! empty($tag->params['field'])) {
                        $fieldRepository = $this->app->make('FieldRepository');

                        $fieldIds = array();

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

                    $categories = array();

                    preg_match_all('#{path=([\042\047]?)(.*?)\\1}#', $tag->tagdata, $pathTags);

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

                        foreach ($pathTags[2] as $i => $path) {
                            $key = substr($pathTags[0][$i], 1, -1);
                            $category[$key] = ee()->functions->create_url($path.$categoryUri);
                        }

                        array_push($categories, $category);
                    }

                    // @TODO parse the file path at the model attribute level using upload pref repository
                    $row[$tag->key] = $categories ? ee()->typography->parse_file_paths(ee()->TMPL->parse_variables($tag->tagdata, $categories)) : '';

                } elseif ($customFieldsEnabled && $entry->channel->fields->hasField($tag->name)) {

                    $row[$tag->key] = '';

                    $value = $entry->{$tag->name};

                    if ($value instanceof AbstractTitleCollection) {
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
                    } elseif ($value instanceof AbstractFilterableCollection) {
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

                if (isset($tag->params['format']) && $entry->{$tag->name} instanceof DateTime) {
                    $format = preg_replace('#%([a-zA-Z])#', '\\1', $tag->params['format']);

                    $row[$tag->key] = $entry->{$tag->name}->format($format);
                }

                switch ($tag->name) {
                    case 'entry_id_path':
                    case 'permalink':
                        $path = isset($tag->params[0]) ? $tag->params[0].'/' : '';
                        $row[$tag->key] = ee()->functions->create_url($path.$entry->entry_id);
                        break;
                    case 'title_permalink':
                    case 'url_title_path':
                        $path = isset($tag->params[0]) ? $tag->params[0].'/' : '';
                        $row[$tag->key] = ee()->functions->create_url($path.$entry->url_title);
                        break;
                    case 'profile_path':
                        $path = isset($tag->params[0]) ? $tag->params[0].'/' : '';
                        $row[$tag->key] = ee()->functions->create_url($path.$entry->author_id);
                        break;
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
}
