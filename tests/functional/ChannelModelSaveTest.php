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
        $this->validateExceptionTest(['site_id' => ''], 'The site id field is required.');
    }

    public function testSiteIdExistsValidation()
    {
        $this->validateExceptionTest(['site_id' => '10'], 'The selected site id is invalid.');
    }

    public function testChannelNameRequiredValidation()
    {
        $this->validateExceptionTest(['channel_name' => ''], 'The channel name field is required.');
    }

    public function testChannelNameAlphaDashValidation()
    {
        $this->validateExceptionTest(['channel_name' => 'Foo bar'], 'The channel name may only contain letters, numbers, and dashes.');
    }

    public function testChannelNameUniqueValidation()
    {
        $this->validateExceptionTest(['channel_name' => 'entries'], 'The channel name has already been taken.');
    }

    public function testChannelTitleRequiredValidation()
    {
        $this->validateExceptionTest(['channel_title' => ''], 'The channel title field is required.');
    }

    public function testChannelLangRequiredValidation()
    {
        $this->validateExceptionTest(['channel_lang' => ''], 'The channel lang field is required.');
    }

    public function testChannelLangLengthValidation()
    {
        $this->validateExceptionTest(['channel_lang' => 'abc'], 'The channel lang must be 2 characters.');
    }

    public function testTotalEntriesRequiredValidation()
    {
        $this->validateExceptionTest(['total_entries' => ''], 'The total entries field is required.');
    }

    public function testTotalEntriesIntegerValidation()
    {
        $this->validateExceptionTest(['total_entries' => 'integer'], 'The total entries must be an integer.');
    }

    public function testTotalCommentsRequiredValidation()
    {
        $this->validateExceptionTest(['total_comments' => ''], 'The total comments field is required.');
    }

    public function testTotalCommentsIntegerValidation()
    {
        $this->validateExceptionTest(['total_comments' => 'integer'], 'The total comments must be an integer.');
    }

    public function testLastEntryDateValidation()
    {
        $this->validateExceptionTest(['total_comments' => 'integer'], 'The total comments must be an integer.');
    }

    public function testLastCommentDateValidation()
    {
        $this->validateExceptionTest(['total_comments' => 'integer'], 'The total comments must be an integer.');
    }

    public function testCatGroupExistsValidation()
    {
        $this->validateExceptionTest(['cat_group' => '1|10'], 'The selected cat group(s) are invalid.');
    }

    public function testStatusGroupExistsValidation()
    {
        $this->validateExceptionTest(['status_group' => '10'], 'The selected status group is invalid.');
    }

    public function testDeftStatusRequiredValidation()
    {
        $this->validateExceptionTest(['deft_status' => ''], 'The deft status field is required.');
    }

    public function testFieldGroupExistsValidation()
    {
        $this->validateExceptionTest(['field_group' => '10'], 'The selected field group is invalid.');
    }

    public function testSearchExcerptExistsValidation()
    {
        $this->validateExceptionTest(['search_excerpt' => '100'], 'The selected search excerpt is invalid.');
    }

    public function testDeftCategoryExistsValidation()
    {
        $this->validateExceptionTest(['deft_category' => '100'], 'The selected deft category is invalid.');
    }

    public function testDeftCommentsRequiredValidation()
    {
        $this->validateExceptionTest(['deft_comments' => ''], 'The deft comments field is required.');
    }

    public function testDeftCommentsYesOrNoValidation()
    {
        $this->validateExceptionTest(['deft_comments' => 'foo'], 'The deft comments field must be y or n.');
    }

    public function testChannelRequireMembershipRequiredValidation()
    {
        $this->validateExceptionTest(['channel_require_membership' => ''], 'The channel require membership field is required.');
    }

    public function testChannelRequireMembershipYesOrNoValidation()
    {
        $this->validateExceptionTest(['channel_require_membership' => 'foo'], 'The channel require membership field must be y or n.');
    }

    public function testChannelHtmlFormattingRequiredValidation()
    {
        $this->validateExceptionTest(['channel_html_formatting' => ''], 'The channel html formatting field is required.');
    }

    public function testChannelHtmlFormattingInValidation()
    {
        $this->validateExceptionTest(['channel_html_formatting' => 'foo'], 'The selected channel html formatting is invalid.');
    }

    public function testChannelAllowImgUrlsRequiredValidation()
    {
        $this->validateExceptionTest(['channel_allow_img_urls' => ''], 'The channel allow img urls field is required.');
    }

    public function testChannelAllowImgUrlsYesOrNoValidation()
    {
        $this->validateExceptionTest(['channel_allow_img_urls' => 'foo'], 'The channel allow img urls field must be y or n.');
    }

    public function testChannelAutoLinkUrlsRequiredValidation()
    {
        $this->validateExceptionTest(['channel_auto_link_urls' => ''], 'The channel auto link urls field is required.');
    }

    public function testChannelAutoLinkUrlsYesOrNoValidation()
    {
        $this->validateExceptionTest(['channel_auto_link_urls' => 'foo'], 'The channel auto link urls field must be y or n.');
    }

    public function testChannelNotifyRequiredValidation()
    {
        $this->validateExceptionTest(['channel_notify' => ''], 'The channel notify field is required.');
    }

    public function testChannelNotifyYesOrNoValidation()
    {
        $this->validateExceptionTest(['channel_notify' => 'foo'], 'The channel notify field must be y or n.');
    }

    public function testCommentSystemEnabledRequiredValidation()
    {
        $this->validateExceptionTest(['comment_system_enabled' => ''], 'The comment system enabled field is required.');
    }

    public function testCommentSystemEnabledYesOrNoValidation()
    {
        $this->validateExceptionTest(['comment_system_enabled' => 'foo'], 'The comment system enabled field must be y or n.');
    }

    public function testCommentRequireMembershipRequiredValidation()
    {
        $this->validateExceptionTest(['comment_require_membership' => ''], 'The comment require membership field is required.');
    }

    public function testCommentRequireMembershipYesOrNoValidation()
    {
        $this->validateExceptionTest(['comment_require_membership' => 'foo'], 'The comment require membership field must be y or n.');
    }

    public function testCommentUseCaptchaRequiredValidation()
    {
        $this->validateExceptionTest(['comment_use_captcha' => ''], 'The comment use captcha field is required.');
    }

    public function testCommentUseCaptchaYesOrNoValidation()
    {
        $this->validateExceptionTest(['comment_use_captcha' => 'foo'], 'The comment use captcha field must be y or n.');
    }

    public function testCommentModerateRequiredValidation()
    {
        $this->validateExceptionTest(['comment_moderate' => ''], 'The comment moderate field is required.');
    }

    public function testCommentModerateYesOrNoValidation()
    {
        $this->validateExceptionTest(['comment_moderate' => 'foo'], 'The comment moderate field must be y or n.');
    }

    public function testCommentMaxCharsRequiredValidation()
    {
        $this->validateExceptionTest(['comment_max_chars' => ''], 'The comment max chars field is required.');
    }

    public function testCommentMaxCharsIntegerValidation()
    {
        $this->validateExceptionTest(['comment_max_chars' => 'integer'], 'The comment max chars must be an integer.');
    }

    public function testCommentTimelockRequiredValidation()
    {
        $this->validateExceptionTest(['comment_timelock' => ''], 'The comment timelock field is required.');
    }

    public function testCommentTimelockIntegerValidation()
    {
        $this->validateExceptionTest(['comment_timelock' => 'integer'], 'The comment timelock must be an integer.');
    }

    public function testCommentRequireEmailRequiredValidation()
    {
        $this->validateExceptionTest(['comment_require_email' => ''], 'The comment require email field is required.');
    }

    public function testCommentRequireEmailYesOrNoValidation()
    {
        $this->validateExceptionTest(['comment_require_email' => 'foo'], 'The comment require email field must be y or n.');
    }

    public function testCommentHtmlFormattingRequiredValidation()
    {
        $this->validateExceptionTest(['comment_html_formatting' => ''], 'The comment html formatting field is required.');
    }

    public function testCommentHtmlFormattingInValidation()
    {
        $this->validateExceptionTest(['comment_html_formatting' => 'foo'], 'The selected comment html formatting is invalid.');
    }

    public function testCommentTextFormattingRequiredValidation()
    {
        $this->validateExceptionTest(['comment_text_formatting' => ''], 'The comment text formatting field is required.');
    }

    public function testCommentTextFormattingInValidation()
    {
        $this->validateExceptionTest(['comment_text_formatting' => 'foo'], 'The selected comment text formatting is invalid.');
    }

    public function testCommentAllowImgUrlsRequiredValidation()
    {
        $this->validateExceptionTest(['comment_allow_img_urls' => ''], 'The comment allow img urls field is required.');
    }

    public function testCommentAllowImgUrlsYesOrNoValidation()
    {
        $this->validateExceptionTest(['comment_allow_img_urls' => 'foo'], 'The comment allow img urls field must be y or n.');
    }

    public function testCommentAutoLinkUrlsRequiredValidation()
    {
        $this->validateExceptionTest(['comment_auto_link_urls' => ''], 'The comment auto link urls field is required.');
    }

    public function testCommentAutoLinkUrlsYesOrNoValidation()
    {
        $this->validateExceptionTest(['comment_auto_link_urls' => 'foo'], 'The comment auto link urls field must be y or n.');
    }

    public function testCommentNotifyRequiredValidation()
    {
        $this->validateExceptionTest(['comment_notify' => ''], 'The comment notify field is required.');
    }

    public function testCommentNotifyYesOrNoValidation()
    {
        $this->validateExceptionTest(['comment_notify' => 'foo'], 'The comment notify field must be y or n.');
    }

    public function testCommentNotifyAuthorsRequiredValidation()
    {
        $this->validateExceptionTest(['comment_notify_authors' => ''], 'The comment notify authors field is required.');
    }

    public function testCommentNotifyAuthorsYesOrNoValidation()
    {
        $this->validateExceptionTest(['comment_notify_authors' => 'foo'], 'The comment notify authors field must be y or n.');
    }

    public function testShowButtonClusterRequiredValidation()
    {
        $this->validateExceptionTest(['show_button_cluster' => ''], 'The show button cluster field is required.');
    }

    public function testShowButtonClusterYesOrNoValidation()
    {
        $this->validateExceptionTest(['show_button_cluster' => 'foo'], 'The show button cluster field must be y or n.');
    }

    public function testEnableVersioningRequiredValidation()
    {
        $this->validateExceptionTest(['enable_versioning' => ''], 'The enable versioning field is required.');
    }

    public function testEnableVersioningYesOrNoValidation()
    {
        $this->validateExceptionTest(['enable_versioning' => 'foo'], 'The enable versioning field must be y or n.');
    }

    public function testLiveLookTemplateExistsValidation()
    {
        $this->validateExceptionTest(['live_look_template' => '100'], 'The selected live look template is invalid');
    }

    public function testUrlTitlePrefixAlphaDashValidation()
    {
        $this->validateExceptionTest(['url_title_prefix' => 'Foo bar'], 'The url title prefix may only contain letters, numbers, and dashes.');
    }
}
