<?php

use rsanchez\Deep\Model\Channel;
use rsanchez\Deep\Model\Field;
use rsanchez\Deep\Repository\FieldRepository;

class ChannelModelSaveTest extends AbstractModelSaveTest
{
    protected function getModelAttributes()
    {
        return [
            'site_id' => '1',
            'channel_name' => 'foo',
            'channel_title' => 'Foo',
            'channel_lang' => 'en',
            'total_entries' => '0',
            'total_comments' => '0',
            'last_entry_date' => '0',
            'last_comment_date' => '0',
            'cat_group' => '1|2',
            'status_group' => '1',
            'deft_status' => 'open',
            'field_group' => '1',
            'search_excerpt' => '1',
            'deft_category' => '1',
            'deft_comments' => 'n',
            'channel_require_membership' => 'y',
            'channel_html_formatting' => 'all',
            'channel_allow_img_urls' => 'y',
            'channel_auto_link_urls' => 'n',
            'channel_notify' => 'n',
            'comment_system_enabled' => 'y',
            'comment_require_membership' => 'n',
            'comment_use_captcha' => 'n',
            'comment_moderate' => 'n',
            'comment_max_chars' => '5000',
            'comment_timelock' => '0',
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
    }

    /**
     * Get the default attributes when testing model attributes
     * @return array  attr => value
     */
    protected function getModelAttributesForTesting()
    {
        $attributes = $this->getModelAttributes();

        $attributes['cat_group'] = explode('|', $attributes['cat_group']);

        return $attributes;
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $field = new Field();

        $fieldRepository = new FieldRepository($field);

        Channel::setFieldRepository($fieldRepository);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        Channel::unsetFieldRepository();
    }

    public function testSiteIdRequiredValidation()
    {
        $this->validateExceptionTest(['site_id' => ''], 'The Site ID field is required.');
    }

    public function testSiteIdExistsValidation()
    {
        $this->validateExceptionTest(['site_id' => '10'], 'The selected Site ID is invalid.');
    }

    public function testChannelNameRequiredValidation()
    {
        $this->validateExceptionTest(['channel_name' => ''], 'The Channel Name field is required.');
    }

    public function testChannelNameAlphaDashValidation()
    {
        $this->validateExceptionTest(['channel_name' => 'Foo bar'], 'The Channel Name may only contain letters, numbers, and dashes.');
    }

    public function testChannelNameUniqueValidation()
    {
        $this->validateExceptionTest(['channel_name' => 'entries'], 'The Channel Name has already been taken.');
    }

    public function testChannelTitleRequiredValidation()
    {
        $this->validateExceptionTest(['channel_title' => ''], 'The Channel Title field is required.');
    }

    public function testChannelLangRequiredValidation()
    {
        $this->validateExceptionTest(['channel_lang' => ''], 'The Channel Lang field is required.');
    }

    public function testChannelLangLengthValidation()
    {
        $this->validateExceptionTest(['channel_lang' => 'abc'], 'The Channel Lang must be 2 characters.');
    }

    public function testTotalEntriesRequiredValidation()
    {
        $this->validateExceptionTest(['total_entries' => ''], 'The Total Entries field is required.');
    }

    public function testTotalEntriesIntegerValidation()
    {
        $this->validateExceptionTest(['total_entries' => 'integer'], 'The Total Entries must be an integer.');
    }

    public function testTotalCommentsRequiredValidation()
    {
        $this->validateExceptionTest(['total_comments' => ''], 'The Total Comments field is required.');
    }

    public function testTotalCommentsIntegerValidation()
    {
        $this->validateExceptionTest(['total_comments' => 'integer'], 'The Total Comments must be an integer.');
    }

    public function testLastEntryDateDateValidation()
    {
        $this->validateExceptionTest(['last_entry_date' => 'foo'], 'The Last Entry Date does not match the format U.', true);
    }

    public function testLastCommentDateDateValidation()
    {
        $this->validateExceptionTest(['last_comment_date' => 'foo'], 'The Last Comment Date does not match the format U.', true);
    }

    public function testCatGroupExistsValidation()
    {
        $this->validateExceptionTest(['cat_group' => '1|10'], 'The selected Category Group(s) are invalid.');
    }

    public function testStatusGroupExistsValidation()
    {
        $this->validateExceptionTest(['status_group' => '10'], 'The selected Status Group is invalid.');
    }

    public function testDeftStatusRequiredValidation()
    {
        $this->validateExceptionTest(['deft_status' => ''], 'The Default Status field is required.');
    }

    public function testFieldGroupExistsValidation()
    {
        $this->validateExceptionTest(['field_group' => '10'], 'The selected Field Group is invalid.');
    }

    public function testSearchExcerptExistsValidation()
    {
        $this->validateExceptionTest(['search_excerpt' => '100'], 'The selected Search Excerpt is invalid.');
    }

    public function testDeftCategoryExistsValidation()
    {
        $this->validateExceptionTest(['deft_category' => '100'], 'The selected Default Category is invalid.');
    }

    public function testDeftCommentsRequiredValidation()
    {
        $this->validateExceptionTest(['deft_comments' => ''], 'The Default Allow Comments field is required.');
    }

    public function testDeftCommentsYesOrNoValidation()
    {
        $this->validateExceptionTest(['deft_comments' => 'foo'], 'The Default Allow Comments field must be y or n.');
    }

    public function testChannelRequireMembershipRequiredValidation()
    {
        $this->validateExceptionTest(['channel_require_membership' => ''], 'The Channel Require Membership field is required.');
    }

    public function testChannelRequireMembershipYesOrNoValidation()
    {
        $this->validateExceptionTest(['channel_require_membership' => 'foo'], 'The Channel Require Membership field must be y or n.');
    }

    public function testChannelHtmlFormattingRequiredValidation()
    {
        $this->validateExceptionTest(['channel_html_formatting' => ''], 'The Channel HTML Formatting field is required.');
    }

    public function testChannelHtmlFormattingInValidation()
    {
        $this->validateExceptionTest(['channel_html_formatting' => 'foo'], 'The selected Channel HTML Formatting is invalid.');
    }

    public function testChannelAllowImgUrlsRequiredValidation()
    {
        $this->validateExceptionTest(['channel_allow_img_urls' => ''], 'The Channel Allow Image URLs field is required.');
    }

    public function testChannelAllowImgUrlsYesOrNoValidation()
    {
        $this->validateExceptionTest(['channel_allow_img_urls' => 'foo'], 'The Channel Allow Image URLs field must be y or n.');
    }

    public function testChannelAutoLinkUrlsRequiredValidation()
    {
        $this->validateExceptionTest(['channel_auto_link_urls' => ''], 'The Channel Auto-Link URLs field is required.');
    }

    public function testChannelAutoLinkUrlsYesOrNoValidation()
    {
        $this->validateExceptionTest(['channel_auto_link_urls' => 'foo'], 'The Channel Auto-Link URLs field must be y or n.');
    }

    public function testChannelNotifyRequiredValidation()
    {
        $this->validateExceptionTest(['channel_notify' => ''], 'The Channel Notify field is required.');
    }

    public function testChannelNotifyYesOrNoValidation()
    {
        $this->validateExceptionTest(['channel_notify' => 'foo'], 'The Channel Notify field must be y or n.');
    }

    public function testCommentSystemEnabledRequiredValidation()
    {
        $this->validateExceptionTest(['comment_system_enabled' => ''], 'The Comment System Enabled field is required.');
    }

    public function testCommentSystemEnabledYesOrNoValidation()
    {
        $this->validateExceptionTest(['comment_system_enabled' => 'foo'], 'The Comment System Enabled field must be y or n.');
    }

    public function testCommentRequireMembershipRequiredValidation()
    {
        $this->validateExceptionTest(['comment_require_membership' => ''], 'The Comment Require Membership field is required.');
    }

    public function testCommentRequireMembershipYesOrNoValidation()
    {
        $this->validateExceptionTest(['comment_require_membership' => 'foo'], 'The Comment Require Membership field must be y or n.');
    }

    public function testCommentUseCaptchaRequiredValidation()
    {
        $this->validateExceptionTest(['comment_use_captcha' => ''], 'The Comment Use Captcha field is required.');
    }

    public function testCommentUseCaptchaYesOrNoValidation()
    {
        $this->validateExceptionTest(['comment_use_captcha' => 'foo'], 'The Comment Use Captcha field must be y or n.');
    }

    public function testCommentModerateRequiredValidation()
    {
        $this->validateExceptionTest(['comment_moderate' => ''], 'The Comment Moderate field is required.');
    }

    public function testCommentModerateYesOrNoValidation()
    {
        $this->validateExceptionTest(['comment_moderate' => 'foo'], 'The Comment Moderate field must be y or n.');
    }

    public function testCommentMaxCharsRequiredValidation()
    {
        $this->validateExceptionTest(['comment_max_chars' => ''], 'The Comment Max Chars field is required.');
    }

    public function testCommentMaxCharsIntegerValidation()
    {
        $this->validateExceptionTest(['comment_max_chars' => 'integer'], 'The Comment Max Chars must be an integer.');
    }

    public function testCommentTimelockRequiredValidation()
    {
        $this->validateExceptionTest(['comment_timelock' => ''], 'The Comment Timelock field is required.');
    }

    public function testCommentTimelockIntegerValidation()
    {
        $this->validateExceptionTest(['comment_timelock' => 'integer'], 'The Comment Timelock must be an integer.');
    }

    public function testCommentRequireEmailRequiredValidation()
    {
        $this->validateExceptionTest(['comment_require_email' => ''], 'The Comment Require Email field is required.');
    }

    public function testCommentRequireEmailYesOrNoValidation()
    {
        $this->validateExceptionTest(['comment_require_email' => 'foo'], 'The Comment Require Email field must be y or n.');
    }

    public function testCommentHtmlFormattingRequiredValidation()
    {
        $this->validateExceptionTest(['comment_html_formatting' => ''], 'The Comment HTML Formatting field is required.');
    }

    public function testCommentHtmlFormattingInValidation()
    {
        $this->validateExceptionTest(['comment_html_formatting' => 'foo'], 'The selected Comment HTML Formatting is invalid.');
    }

    public function testCommentTextFormattingRequiredValidation()
    {
        $this->validateExceptionTest(['comment_text_formatting' => ''], 'The Comment Text Formatting field is required.');
    }

    public function testCommentTextFormattingInValidation()
    {
        $this->validateExceptionTest(['comment_text_formatting' => 'foo'], 'The selected Comment Text Formatting is invalid.');
    }

    public function testCommentAllowImgUrlsRequiredValidation()
    {
        $this->validateExceptionTest(['comment_allow_img_urls' => ''], 'The Comment Allow Image URLs field is required.');
    }

    public function testCommentAllowImgUrlsYesOrNoValidation()
    {
        $this->validateExceptionTest(['comment_allow_img_urls' => 'foo'], 'The Comment Allow Image URLs field must be y or n.');
    }

    public function testCommentAutoLinkUrlsRequiredValidation()
    {
        $this->validateExceptionTest(['comment_auto_link_urls' => ''], 'The Comment Auto-Link URLs field is required.');
    }

    public function testCommentAutoLinkUrlsYesOrNoValidation()
    {
        $this->validateExceptionTest(['comment_auto_link_urls' => 'foo'], 'The Comment Auto-Link URLs field must be y or n.');
    }

    public function testCommentNotifyRequiredValidation()
    {
        $this->validateExceptionTest(['comment_notify' => ''], 'The Comment Notify field is required.');
    }

    public function testCommentNotifyYesOrNoValidation()
    {
        $this->validateExceptionTest(['comment_notify' => 'foo'], 'The Comment Notify field must be y or n.');
    }

    public function testCommentNotifyAuthorsRequiredValidation()
    {
        $this->validateExceptionTest(['comment_notify_authors' => ''], 'The Comment Notify Authors field is required.');
    }

    public function testCommentNotifyAuthorsYesOrNoValidation()
    {
        $this->validateExceptionTest(['comment_notify_authors' => 'foo'], 'The Comment Notify Authors field must be y or n.');
    }

    public function testShowButtonClusterRequiredValidation()
    {
        $this->validateExceptionTest(['show_button_cluster' => ''], 'The Show Button Cluster field is required.');
    }

    public function testShowButtonClusterYesOrNoValidation()
    {
        $this->validateExceptionTest(['show_button_cluster' => 'foo'], 'The Show Button Cluster field must be y or n.');
    }

    public function testEnableVersioningRequiredValidation()
    {
        $this->validateExceptionTest(['enable_versioning' => ''], 'The Enable Versioning field is required.');
    }

    public function testEnableVersioningYesOrNoValidation()
    {
        $this->validateExceptionTest(['enable_versioning' => 'foo'], 'The Enable Versioning field must be y or n.');
    }

    public function testLiveLookTemplateExistsValidation()
    {
        $this->validateExceptionTest(['live_look_template' => '100'], 'The selected Live Look Template is invalid');
    }

    public function testUrlTitlePrefixAlphaDashValidation()
    {
        $this->validateExceptionTest(['url_title_prefix' => 'Foo bar'], 'The URL Title Prefix may only contain letters, numbers, and dashes.');
    }
}
