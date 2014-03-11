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
use rsanchez\Deep\Collection\AbstractTitleCollection;
use rsanchez\Deep\Collection\EntryCollection;
use DateTime;

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
        Deep::bootEloquent(ee());
    }

    /**
     * Parse TMPL->tagdata with the specified collection of entries
     *
     * @param  \rsanchez\Deep\Collection\AbstractTitleCollection $entries
     * @return string
     */
    protected function parse(AbstractTitleCollection $entries)
    {
        $tagdata = ee()->TMPL->tagdata;

        $customFieldsEnabled = $entries instanceof EntryCollection;

        preg_match_all('#{((url_title_path|title_permalink)=([\042\047])(.*?)\\3)}#', $tagdata, $urlTitlePathMatches);
        preg_match_all('#{(entry_id_path=([\042\047])(.*?)\\2)}#', $tagdata, $entryIdPathMatches);
        preg_match_all('#{((.*?) format=([\042\047])(.*?)\\3)}#', $tagdata, $dateMatches);

        $singleTags = array();
        $pairTags = array();

        if ($customFieldsEnabled) {
            foreach (array_keys(ee()->TMPL->var_single) as $tag) {
                $spacePosition = strpos($tag, ' ');

                if ($spacePosition === false) {
                    $name = $tag;
                    $params = array();
                } else {
                    $name = substr($tag, 0, $spacePosition);
                    $params = ee()->functions->assign_parameters(substr($tag, $spacePosition));
                }

                $singleTags[] = (object) array(
                    'name' => $name,
                    'key' => $tag,
                    'params' => $params,
                    'tagdata' => '',
                );
            }

            foreach (ee()->TMPL->var_pair as $tag => $params) {
                $spacePosition = strpos($tag, ' ');

                $name = $spacePosition === false ? $tag : substr($tag, 0, $spacePosition);

                preg_match_all('#{('.preg_quote($tag).'}(.*?){/'.preg_quote($name).')}#s', $tagdata, $matches);

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

        foreach ($entries as $entry) {
            $row = array();

            if ($customFieldsEnabled) {
                foreach ($pairTags as $tag) {
                    if ($entry->channel->fields->hasField($tag->name)) {
                        $row[$tag->key] = $entry->{$tag->name}->isEmpty() ? '' : ee()->TMPL->parse_variables($tag->tagdata, $entry->{$tag->name}->toArray());
                    }
                }

                foreach ($singleTags as $tag) {
                    if ($entry->channel->fields->hasField($tag->name)) {
                        $row[$tag->key] = (string) $entry->{$tag->name};
                    }
                }
            }

            foreach ($urlTitlePathMatches[1] as $i => $key) {
                $row[$key] = ee()->functions->create_url($urlTitlePathMatches[4][$i]);
            }

            foreach ($entryIdPathMatches[1] as $i => $key) {
                $row[$key] = ee()->functions->create_url($entryIdPathMatches[3][$i]);
            }

            foreach ($dateMatches[1] as $i => $key) {
                $name = $dateMatches[2][$i];
                $format = preg_replace('#%([a-zA-Z])#', '\\1', $dateMatches[4][$i]);

                $row[$key] = ($entry->$name instanceof DateTime) ? $entry->$name->format($format) : '';
            }

            $row = array_merge($row, $entry->getOriginal(), $entry->channel->toArray());

            if (isset($entry->member)) {
                foreach ($entry->member->toArray() as $key => $value) {
                    $row[$key] = $value;
                }
            }

            $variables[] = $row;
        }

        if (! $variables) {
            return ee()->TMPL->no_results();
        }

        return ee()->TMPL->parse_variables($tagdata, $variables);
    }
}
