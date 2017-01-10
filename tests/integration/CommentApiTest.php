<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/BaseApiTest.php';

class CommentApiTest extends BaseApiTest
{
    protected $projectName = 'My project to test comments';
    private $commentId = 0;

    public function testAll()
    {
        $this->assertCreateTeamProject();
        $this->assertCreateTask();
        $this->assertCreateComment();
        $this->assertUpdateComment();
        $this->assertGetAllComments();
        $this->assertRemoveComment();
    }

    public function assertCreateComment()
    {
        $this->commentId = $this->app->execute('createComment', [
            'task_id' => $this->taskId,
            'user_id' => 1,
            'content' => 'foobar',
        ]);

        $this->assertNotFalse($this->commentId);
    }

    public function assertGetComment()
    {
        $comment = $this->app->getComment($this->commentId);
        $this->assertNotFalse($comment);
        $this->assertNotEmpty($comment);
        $this->assertEquals(1, $comment['user_id']);
        $this->assertEquals('foobar', $comment['comment']);
    }

    public function assertUpdateComment()
    {
        $this->assertTrue($this->app->execute('updateComment', [
            'id'      => $this->commentId,
            'content' => 'test',
        ]));

        $comment = $this->app->getComment($this->commentId);
        $this->assertEquals('test', $comment['comment']);
    }

    public function assertGetAllComments()
    {
        $comments = $this->app->getAllComments($this->taskId);
        $this->assertCount(1, $comments);
        $this->assertEquals('test', $comments[0]['comment']);
    }

    public function assertRemoveComment()
    {
        $this->assertTrue($this->app->removeComment($this->commentId));
        $this->assertFalse($this->app->removeComment($this->commentId));
    }
}
