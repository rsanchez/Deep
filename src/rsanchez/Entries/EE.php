<?php

namespace rsanchez\Entries;

use \rsanchez\Entries\Entries\Field;
use \rsanchez\Entries\Entries\Entry;
use \rsanchez\Entries\EE\Template;

class EE {
  public function __construct() {
    ee()->load->library(array('functions', 'localize'));
  }

  public function create_url() {
    return call_user_func_array(array(ee()->functions, __FUNCTION__), func_get_args());
  }

  public function format_date() {
  	$args = func_get_args();
    return call_user_func_array(array(ee()->localize, __FUNCTION__), $args);
  }

  public function call_fieldtype(Channel $channel, Entry $entry, Field $field, $params = array(), $modifier = 'tag') {
		ee()->load->library('api');
		ee()->load->helper('custom_field');
		ee()->load->library('typography');
		ee()->api->instantiate('channel_fields');

		if ( ! isset(ee()->api_channel_fields->custom_fields[$field->field_id]))
		{
			$settings = $field->field_settings;
			$settings['field_type'] = $field->field_type;
			$settings['field_fmt'] = $field->field_fmt;

			ee()->api_channel_fields->field_types[$field->field_type] = ee()->api_channel_fields->include_handler($field->field_type);
			ee()->api_channel_fields->custom_fields[$field->field_id] = $field->field_type;
			ee()->api_channel_fields->set_settings($field->field_id, $settings);

			//since it will already be in our settings array, leave it blank
			ee()->api_channel_fields->global_settings[$field->field_type] = array();
		}

		$ft = ee()->api_channel_fields->setup_handler($field->field_id, TRUE);

		$ft->row = array_merge($entry->toArray(), $channel->toArray());

		$data = ee()->api_channel_fields->apply('pre_process', array($field->value));

		$TMPL = NULL;

		if (isset(ee()->TMPL))
		{
			$TMPL = ee()->TMPL;
		}

		ee()->TMPL = new Template;

		$output = ee()->api_channel_fields->apply('replace_'.$modifier, array(
			'data' => $data,
			'params' => $params,
		));

		if ($TMPL) {
			ee()->TMPL = $TMPL;
		} else {
			unset(ee()->TMPL);
		}

		return $output;
  }
}