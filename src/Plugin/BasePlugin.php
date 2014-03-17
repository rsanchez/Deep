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
use rsanchez\Deep\Collection\EntryCollection;
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
        if (! isset(ee()->deep)) {
            ee()->deep = new Deep(ee()->config->config);

            ee()->deep->bootEloquent(ee());
        }
    }

    /**
     * Parse a plugin tag pair equivalent to channel:entries
     *
     * @param  Closure|null $callback receieves a query builder object as the first parameter
     * @return string
     */
    protected function parse(Closure $callback = null)
    {
        $disabled = array();

        if ($disable = ee()->TMPL->fetch_param('disable')) {
            $disabled = explode('|', $disable);
        }

        ee()->load->library('pagination');

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

        $identifier = $customFieldsEnabled ? 'Entry' : 'Title';

        $query = ee()->deep->make($identifier)->tagparams(ee()->TMPL->tagparams);

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

            $singleTags[] = (object) array(
                'name' => $name,
                'key' => $tag,
                'params' => $params,
                'tagdata' => '',
            );
        }

        if ($customFieldsEnabled) {
            foreach (ee()->TMPL->var_pair as $tag => $params) {
                $spacePosition = strpos($tag, ' ');

                $name = $spacePosition === false ? $tag : substr($tag, 0, $spacePosition);

                preg_match_all('#{('.preg_quote($tag).'}(.*?){/'.preg_quote($name).')}#s', ee()->TMPL->tagdata, $matches);

                foreach ($matches[1] as $i => $key) {
                    $pairTags[] = (object) array(
                        'name' => $name,
                        'key' => $key,
                        'params' => $params,
                        'tagdata' => $matches[2][$i],
                    );
                }
            }
        }

        $variables = array();

        foreach ($entries as $i => $entry) {
            $row = array(
                'absolute_count' => $paginationOffset + $i + 1,
                'absolute_results' => $paginationCount,
                'channel' => $entry->channel->channel_name,
                'channel_short_name' => $entry->channel->channel_name,
                'comment_auto_path' => $entry->channel->comment_url,
                'comment_entry_id_auto_path' => $entry->channel->comment_url.'/'.$entry->entry_id,
                'comment_url_title_auto_path' => $entry->channel->comment_url.'/'.$entry->url_title,
                'entry_site_id' => $entry->site_id,
                'page_uri' => $entry->page_uri,
                'page_url' => ee()->functions->create_url($entry->page_uri),
            );

            if ($customFieldsEnabled) {
                foreach ($pairTags as $tag) {
                    if ($entry->channel->fields->hasField($tag->name)) {

                        $row[$tag->key] = '';

                        $value = $entry->{$tag->name};

                        if ($value instanceof Collection) {
                            if (isset($tag->params['row_id'])) {
                                $rowId = $tag->params['row_id'];

                                $value = $value->filter(function ($row) use ($rowId) {
                                    return $row->row_id == $rowId;
                                });
                            }

                            if (isset($tag->params['limit']) || isset($tag->params['offset'])) {
                                $offset = isset($tag->params['offset']) ? $tag->params['offset'] : 0;
                                $limit = isset($tag->params['limit']) ? $tag->params['limit'] : null;
                                $value = $value->slice($offset, $limit);
                            }

                            $value = $value->toArray();
                        } elseif (is_object($value) && method_exists($value, 'toArray')) {
                            $value = $value->toArray();
                        } elseif ($value) {
                            $value = (string) $value;
                        }

                        if ($value) {
                            $row[$tag->key] = ee()->TMPL->parse_variables($tag->tagdata, $value);

                            if (isset($tag->params['backspace'])) {
                                $row[$tag->key] = substr($row[$tag->key], 0, -$tag->params['backspace']);
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
                }
            }

            if ($categoriesEnabled) {
                $row['categories'] = $entry->categories->toArray();
            }

            $row = array_merge($row, $entry->getOriginal(), $entry->channel->toArray());

            if ($membersEnabled) {
                foreach ($entry->member->toArray() as $key => $value) {
                    $row[$key] = $value;
                }

                $row['author'] = $entry->member->screen_name ?: $entry->member->username;
                $row['avatar_url'] = $entry->member->avatar_filename ? ee()->config->item('avatar_url').$entry->member->avatar_filename : '';
                $row['avatar_image_height'] = $entry->member->avatar_height;
                $row['avatar_image_width'] = $entry->member->avatar_width;
                $row['photo_url'] = $entry->member->photo_filename ? ee()->config->item('photo_url').$entry->member->photo_filename : '';
                $row['photo_image_height'] = $entry->member->photo_height;
                $row['photo_image_width'] = $entry->member->photo_width;
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
