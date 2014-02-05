<?php

namespace rsanchez\Deep;

use rsanchez\Deep\Channel\Field\Field;
use rsanchez\Deep\Entry\Entry;
use rsanchez\Deep\EE\Template;
use CI_Controller;

class FieldtypeHandler
{
    public function call(CI_Controller $ee, Entry $entry, Channel $field, $params = array(), $modifier = 'tag')
    {
        $ee->load->library('api');
        $ee->load->helper('custom_field');
        $ee->load->library('typography');
        $ee->api->instantiate('channel_fields');

        $api =& $ee->api_channel_fields;

        if (! isset($api->custom_fields[$field->field_id])) {
            $settings = $field->field_settings;
            $settings['field_type'] = $field->field_type;
            $settings['field_fmt'] = $field->field_fmt;

            $api->field_types[$field->field_type] = $api->include_handler($field->field_type);
            $api->custom_fields[$field->field_id] = $field->field_type;
            $api->set_settings($field->field_id, $settings);

            //since it will already be in our settings array, leave it blank
            $api->global_settings[$field->field_type] = array();
        }

        $ft = $ee->api_channel_fields->setup_handler($field->field_id, true);

        $ft->row = array_merge($entry->toArray(), $entry->channel->toArray());

        $data = $api->apply('pre_process', array($entry->{$field->name()}));

        if (! isset($ee->TMPL)) {
            $ee->load->library('template', null, 'TMPL');
        }

        $TMPL = new Template($ee);

        $output = $api->apply('replace_'.$modifier, array(
            'data' => $data,
            'params' => $params,
        ));

        unset($TMPL);

        return $output;
    }
}
