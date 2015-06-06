<?php

use rsanchez\Deep\Model\Comment;

class CommentModelSaveTest extends AbstractModelSaveTest
{
    protected function getModelAttributes()
    {
        return [
            'site_id' => '1',
            'entry_id' => '1',
            'channel_id' => '1',
            'author_id' => '1',
            'status' => 'o',
            'name' => 'John Doe',
            'email' => 'jdoe@aol.com',
            'url' => 'http://aol.com',
            'location' => 'USA',
            'ip_address' => '127.0.0.1',
            'comment' => 'foo',
        ];
    }

    public function testSiteIdRequiredValidation()
    {
        $this->validateExceptionTest(['site_id' => ''], 'The Site ID field is required.');
    }

    public function testSiteIdExistsValidation()
    {
        $this->validateExceptionTest(['site_id' => '10'], 'The selected Site ID is invalid.');
    }

    public function testEntryIdRequiredValidation()
    {
        $this->validateExceptionTest(['entry_id' => ''], 'The Entry ID field is required.');
    }

    public function testEntryIdExistsValidation()
    {
        $this->validateExceptionTest(['entry_id' => '99999'], 'The selected Entry ID is invalid.');
    }

    public function testChannelIdRequiredValidation()
    {
        $this->validateExceptionTest(['channel_id' => ''], 'The Channel ID field is required.');
    }

    public function testChannelIdExistsValidation()
    {
        $this->validateExceptionTest(['channel_id' => '10'], 'The selected Channel ID is invalid.');
    }

    public function testAuthorIdExistsValidation()
    {
        $this->validateExceptionTest(['author_id' => '10'], 'The selected Author ID is invalid.');
    }

    public function testStatusRequiredValidation()
    {
        $this->validateExceptionTest(['status' => ''], 'The Status field is required.');
    }

    public function testStatusInValidation()
    {
        $this->validateExceptionTest(['status' => 'foo'], 'The selected Status is invalid.');
    }

    public function testNameRequiredValidation()
    {
        $this->validateExceptionTest(['name' => ''], 'The Name field is required.');
    }

    public function testEmailEmailValidation()
    {
        $this->validateExceptionTest(['email' => 'foo'], 'The Email must be a valid email address.');
    }

    public function testUrlUrlValidation()
    {
        $this->validateExceptionTest(['url' => 'foo'], 'The URL format is invalid.');
    }

    public function testIpAddressIpValidation()
    {
        $this->validateExceptionTest(['ip_address' => 'foo'], 'The IP Address must be a valid IP address.');
    }

    public function testCommentDateDateValidation()
    {
        $this->validateExceptionTest(['comment_date' => 'foo'], 'The Comment Date does not match the format U.', true);
    }

    public function testEditDateDateValidation()
    {
        $this->validateExceptionTest(['edit_date' => 'foo'], 'The Edit Date does not match the format U.', true);
    }

    public function testCommentRequiredValidation()
    {
        $this->validateExceptionTest(['comment' => ''], 'The Comment field is required.');
    }
}
