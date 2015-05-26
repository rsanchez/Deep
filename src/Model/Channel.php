<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use rsanchez\Deep\Collection\ChannelCollection;
use rsanchez\Deep\Relations\HasManyFromRepository;
use rsanchez\Deep\Validation\Factory as ValidatorFactory;

/**
 * Model for the channels table
 */
class Channel extends Model
{
    use HasFieldRepositoryTrait;

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $table = 'channels';

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $primaryKey = 'channel_id';

    /**
     * {@inheritdoc}
     */
    protected $hidden = ['fields'];

    /**
     * {@inheritdoc}
     */
    protected $rules = [
        'site_id' => 'required|exists:sites,site_id',
        'channel_name' => 'required|alpha_dash|unique:channels,channel_name',
        'channel_title' => 'required',
        'channel_lang' => 'required|length:2',
        'total_entries' => 'required|integer',
        'total_comments' => 'required|integer',
        'last_entry_date' => 'date_format:U',
        'last_comment_date' => 'date_format:U',
        'cat_group' => 'pipe_exists:category_groups,group_id',
        'status_group' => 'exists:status_groups,group_id',
        'deft_status' => 'required',
        'field_group' => 'exists:field_groups,group_id',
        'search_excerpt' => 'exists:channel_fields,field_id',
        'deft_category' => [
            ['exists', 'categories', 'cat_id'],
        ],
        'deft_comments' => 'required|yes_or_no',
        'channel_require_membership' => 'required|yes_or_no',
        'channel_html_formatting' => 'required|in:all,safe,none',
        'channel_allow_img_urls' => 'required|yes_or_no',
        'channel_auto_link_urls' => 'required|yes_or_no',
        'channel_notify' => 'required|yes_or_no',
        'comment_system_enabled' => 'required|yes_or_no',
        'comment_require_membership' => 'required|yes_or_no',
        'comment_use_captcha' => 'required|yes_or_no',
        'comment_moderate' => 'required|yes_or_no',
        'comment_max_chars' => 'required|integer',
        'comment_timelock' => 'required|integer',
        'comment_require_email' => 'required|yes_or_no',
        'comment_text_formatting' => 'required|in:xhtml,br,none',
        'comment_html_formatting' => 'required|in:all,safe,none',
        'comment_allow_img_urls' => 'required|yes_or_no',
        'comment_auto_link_urls' => 'required|yes_or_no',
        'comment_notify' => 'required|yes_or_no',
        'comment_notify_authors' => 'required|yes_or_no',
        'show_button_cluster' => 'required|yes_or_no',
        'enable_versioning' => 'required|yes_or_no',
        'url_title_prefix' => 'alpha_dash',
        'live_look_template' => 'exists_or_zero:templates,template_id',
    ];

    /**
     * {@inheritdoc}
     */
    protected $attributes = [
        'site_id' => '1',
        'channel_lang' => 'en',
        'total_entries' => '0',
        'total_comments' => '0',
        'last_entry_date' => '0',
        'last_comment_date' => '0',
        'deft_status' => 'open',
        'deft_comments' => 'y',
        'channel_require_membership' => 'y',
        'channel_html_formatting' => 'all',
        'channel_allow_img_urls' => 'y',
        'channel_auto_link_urls' => 'n',
        'channel_notify' => 'n',
        'comment_system_enabled' => 'y',
        'comment_require_membership' => 'n',
        'comment_use_captcha' => 'n',
        'comment_moderate' => 'n',
        'comment_max_chars' => 'required|integer',
        'comment_timelock' => 'required|integer',
        'comment_require_email' => 'y',
        'comment_text_formatting' => 'none',
        'comment_html_formatting' => 'none',
        'comment_allow_img_urls' => 'n',
        'comment_auto_link_urls' => 'y',
        'comment_notify' => 'n',
        'comment_notify_authors' => 'n',
        'show_button_cluster' => 'y',
        'enable_versioning' => 'n',
        'live_look_template' => '0',
    ];

    /**
     * {@inheritdoc}
     */
    public function getUpdateValidationRules(ValidatorFactory $validatorFactory, PropertyInterface $property = null)
    {
        $rules = $this->getDefaultValidationRules($validatorFactory, $property);

        $rules['channel_name'] .= sprintf(',%s,channel_id', $this->channel_id);

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValidationRules(ValidatorFactory $validatorFactory, PropertyInterface $property = null)
    {
        $rules = $this->rules;

        if ($this->field_group) {
            $rules['search_excerpt'] .= sprintf(',group_id,%s', $this->field_group);
        }

        if ($this->cat_group) {
            $rules['deft_category'][0][] = 'group_id';
            $rules['deft_category'][0][] = $this->cat_group;
        }

        if ($this->status_group) {
            $rules['deft_status'] .= sprintf('|exists:statuses,status,group_id,%s', $this->status_group);
        } else {
            $rules['deft_status'] .= '|in:open,closed';
        }

        return $rules;
    }

    /**
     * {@inheritdoc}
     *
     * @param  array                                       $models
     * @return \rsanchez\Deep\Collection\ChannelCollection
     */
    public function newCollection(array $models = array())
    {
        return new ChannelCollection($models);
    }

    /**
     * Define the Fields Eloquent relationship
     * @return \rsanchez\Deep\Relations\HasManyFromRepository
     */
    public function fields()
    {
        return new HasManyFromRepository(
            static::getFieldRepository()->getModel()->newQuery(),
            $this,
            'channel_fields.group_id',
            'field_group',
            static::getFieldRepository(),
            'getFieldsByGroup'
        );
    }

    /**
     * Get channel fields of the specified type
     * @param  string                                    $type name of a fieldtype
     * @return \rsanchez\Deep\Collection\FieldCollection
     */
    public function fieldsByType($type)
    {
        return $this->fields->getFieldsByFieldtype($type);
    }

    /**
     * Get the cat_group attribute as an array
     * @param  string $data pipe-delimited list
     * @return array  of category group IDs
     */
    public function getCatGroupAttribute($data)
    {
        return $data ? explode('|', $data) : array();
    }

    /**
     * Return the channel_name when cast to string
     *
     * @var string
     */
    public function __toString()
    {
        return $this->channel_name;
    }
}
