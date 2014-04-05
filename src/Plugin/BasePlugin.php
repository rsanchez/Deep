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
    }

    /**
     * Parse a plugin tag pair equivalent to channel:entries
     *
     * @param  Closure|null $callback receieves a query builder object as the first parameter
     * @return string
     */
    protected function parseEntries(Closure $callback = null)
    {
        if ($prefix = ee()->TMPL->fetch_param('var_prefix')) {
            $prefix = rtrim($prefix, ':').':';
            $prefixLength = strlen($prefix);
        } else {
            $prefix = '';
            $prefixLength = 0;
        }

        $disabled = array();

        if ($disable = ee()->TMPL->fetch_param('disable')) {
            $disabled = explode('|', $disable);
        }

        ee()->load->library(array('pagination', 'typography'));

        $pagination = ee()->pagination->create();

        $limit = ee()->TMPL->fetch_param('limit');

        ee()->TMPL->tagdata = $pagination->prepare(ee()->TMPL->tagdata);

        $customFieldsEnabled = ! in_array('custom_fields', $disabled);
        $membersEnabled = ! in_array('members', $disabled);
        $paginationEnabled = ! in_array('pagination', $disabled);
        $categoriesEnabled = ! in_array('categories', $disabled);

        if ($limit && $paginationEnabled) {
            unset(ee()->TMPL->tagparams['offset']);
        } else {
            $pagination->paginate = false;
        }

        $builderClass = $customFieldsEnabled ? 'Entries' : 'Titles';

        $query = call_user_func(array('\\rsanchez\\Deep\\App\\'.$builderClass, 'tagparams'), ee()->TMPL->tagparams);

        if ($categoriesEnabled) {
            $query->with('categories');
        }

        if ($membersEnabled) {
            $query->with('author');
        }

        $tablePrefix = $query->getQuery()->getConnection()->getTablePrefix();

        if (strpos(ee()->TMPL->tagdata, 'comment_subscriber_total') !== false) {
            $query->addSelect($query->getQuery()->getConnection()->raw("(SELECT COUNT(*) FROM {$tablePrefix}comment_subscriptions WHERE {$tablePrefix}comment_subscriptions.entry_id = {$tablePrefix}channel_titles.entry_id) AS comment_subscriber_total"));
        }

        $paginationOffset = 0;

        $paginationCount = $limit;

        if ($pagination->paginate) {
            $paginationCount = $query->getPaginationCount();

            if (preg_match('#P(\d+)/?$#', ee()->uri->uri_string(), $match)) {
                $query->skip($match[1]);

                $paginationOffset = $match[1];
            }

            $pagination->build($paginationCount, $limit);
        }

        if (is_callable($callback)) {
            $callback($query);
        }

        $entries = $query->get();

        $singleTags = array();
        $pairTags = array();

        foreach (array_keys(ee()->TMPL->var_single) as $tag) {
            $spacePosition = strpos($tag, ' ');

            if ($spacePosition !== false) {
                $name = substr($tag, 0, $spacePosition);
                $params = ee()->functions->assign_parameters(substr($tag, $spacePosition));
            } elseif (preg_match('#^([a-z_]+)=([\042\047]?)?(.*?)\\2$#', $tag, $match)) {
                $name = $match[1];
                $params = $match[2] ? array($match[3]) : array('');
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

        $parsePairTags = $customFieldsEnabled || $categoriesEnabled;

        if ($parsePairTags) {
            foreach (ee()->TMPL->var_pair as $tag => $params) {
                $spacePosition = strpos($tag, ' ');

                $name = $spacePosition === false ? $tag : substr($tag, 0, $spacePosition);

                if ($prefix && strncmp($name, $prefix, $prefixLength) !== 0) {
                    continue;
                }

                preg_match_all('#{('.preg_quote($tag).'}(.*?){/'.preg_quote($name).')}#s', ee()->TMPL->tagdata, $matches);

                foreach ($matches[1] as $i => $key) {
                    $pairTags[] = (object) array(
                        'name' => $prefix ? substr($name, $prefixLength) : $name,
                        'key' => $key,
                        'params' => $params ?: array(),
                        'tagdata' => $matches[2][$i],
                    );
                }
            }
        }

        $variables = array();

        foreach ($entries as $i => $entry) {
            $row = array(
                $prefix.'absolute_count' => $paginationOffset + $i + 1,
                $prefix.'absolute_results' => $paginationCount,
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

            if ($parsePairTags) {
                foreach ($pairTags as $tag) {
                    if ($categoriesEnabled && $tag->name === 'categories') {

                        $categories = array();

                        preg_match_all('#{path=([\042\047]?)(.*?)\\1}#', $tag->tagdata, $pathTags);

                        foreach ($entry->categories->tagparams($tag->params) as $categoryModel) {
                            $categoryUri = ee()->config->item('use_category_name') === 'y'
                                ? '/'.ee()->config->item('reserved_category_word').'/'.$categoryModel->cat_url_title
                                : '/C'.$categoryModel->cat_id;

                            $category = array(
                                'active' => (bool) preg_match('#'.preg_quote($categoryUri).'(\/|\/P\d+\/?)?$#', ee()->uri->uri_string()),
                                'category_description' => $categoryModel->cat_description,
                                'category_group' => $categoryModel->grou_id,
                                'category_id' => $categoryModel->cat_id,
                                'parent_id' => $categoryModel->parent_id,
                                'category_image' => $categoryModel->cat_image,
                                'category_name' => $categoryModel->cat_name,
                                'category_url_title' => $categoryModel->cat_url_title,
                            );

                            foreach ($pathTags[2] as $i => $path) {
                                $category[substr($pathTags[0][$i], 1, -1)] = ee()->functions->create_url($path.$categoryUri);
                            }

                            array_push($categories, $category);
                        }

                        // @TODO parse the file path at the model attribute level using upload pref repository
                        $row[$tag->key] = $categories ? ee()->typography->parse_file_paths(ee()->TMPL->parse_variables($tag->tagdata, $categories)) : '';

                    } elseif ($customFieldsEnabled && $entry->channel->fields->hasField($tag->name)) {

                        $row[$tag->key] = '';

                        $value = $entry->{$tag->name};

                        if ($value instanceof AbstractFilterableCollection) {
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
                    if ($entry->channel->fields->hasField($tag->name)) {
                        $row[$tag->key] = (string) $entry->{$tag->name};
                    }
                }
            }

            foreach ($singleTags as $tag) {
                if (isset($tag->params['format'])) {
                    $format = preg_replace('#%([a-zA-Z])#', '\\1', $tag->params['format']);

                    $row[$tag->key] = ($entry->{$tag->name} instanceof DateTime) ? $entry->{$tag->name}->format($format) : '';
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

            if ($membersEnabled) {
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
                $row[$prefix.'url_or_email_as_author'] = '<a href="'.($entry->author->url ?: 'mailto:'.$entry->author->email).'">'.$row['author'].'</a>';
                $row[$prefix.'url_or_email_as_link'] = '<a href="'.($entry->author->url ?: 'mailto:'.$entry->author->email).'">'.$row['url_or_email'].'</a>';
            }

            $variables[] = $row;
        }

        if (! $variables) {
            return ee()->TMPL->no_results();
        }

        $output = ee()->TMPL->parse_variables(ee()->TMPL->tagdata, $variables);

        if ($pagination->paginate) {
            $output = $pagination->render($output);
        }

        if ($backspace = ee()->TMPL->fetch_param('backspace')) {
            $output = substr($output, 0, -$backspace);
        }

        return $output;
    }
}
