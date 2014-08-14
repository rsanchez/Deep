<?php

use rsanchez\Deep\Model\Comment;

class CommentModelTest extends PHPUnit_Framework_TestCase
{
    public function testCollection()
    {
        $query = Comment::all();

        $this->assertInstanceOf('\\Illuminate\\Database\\Eloquent\\Collection', $query);
    }

    public function testAuthorRelationship()
    {
        $query = Comment::all();

        $this->assertInstanceOf('\\rsanchez\\Deep\\Model\\Member', $query->first()->author);
    }
}
