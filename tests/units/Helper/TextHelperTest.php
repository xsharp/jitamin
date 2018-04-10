<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/../Base.php';

use Jitamin\Helper\TextHelper;
use Jitamin\Model\ProjectModel;
use Jitamin\Model\TaskModel;

class TextHelperTest extends Base
{
    public function testMarkdownTaskLink()
    {
        $helper = new TextHelper($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskModel = new TaskModel($this->container);

        $this->assertEquals(1, $projectModel->create(['name' => 'Project #1']));
        $this->assertTrue($projectModel->enablePublicAccess(1));
        $this->assertEquals(1, $taskModel->create(['title' => 'Task #1', 'project_id' => 1]));
        $project = $projectModel->getById(1);

        $this->assertEquals('<p>Test</p>', $helper->markdown('Test'));

        $this->assertEquals(
            '<p>Task <a href="t/123">#123</a></p>',
            $helper->markdown('Task #123')
        );

        $this->assertEquals(
            '<p>Task #123</p>',
            $helper->markdown('Task #123', true)
        );

        $this->assertEquals(
            '<p>Task <a href="public/task/1/'.$project['token'].'">#1</a></p>',
            $helper->markdown('Task #1', true)
        );

        $this->assertEquals(
            '<p>Check that: <a href="http://stackoverflow.com/questions/1732348/regex-match-open-tags-except-xhtml-self-contained-tags/1732454#1732454">http://stackoverflow.com/questions/1732348/regex-match-open-tags-except-xhtml-self-contained-tags/1732454#1732454</a></p>',
            $helper->markdown(
                'Check that: http://stackoverflow.com/questions/1732348/regex-match-open-tags-except-xhtml-self-contained-tags/1732454#1732454'
            )
        );
    }

    public function testMarkdownUserLink()
    {
        $h = new TextHelper($this->container);
        $this->assertEquals('<p>Text <a href="profile/1" class="user-mention-link">@admin</a> @notfound</p>', $h->markdown('Text @admin @notfound'));
        $this->assertEquals('<p>Text @admin @notfound</p>', $h->markdown('Text @admin @notfound', true));
    }

    public function testMarkdownAttribute()
    {
        $helper = new TextHelper($this->container);
        $this->assertEquals('&lt;p&gt;&Ccedil;a marche&lt;/p&gt;', $helper->markdownAttribute('Ça marche'));
        $this->assertEquals('&lt;p&gt;Test with &amp;quot;double quotes&amp;quot;&lt;/p&gt;', $helper->markdownAttribute('Test with "double quotes"'));
        $this->assertEquals('&lt;p&gt;Test with &#039;single quotes&#039;&lt;/p&gt;', $helper->markdownAttribute("Test with 'single quotes'"));
    }

    public function testFormatBytes()
    {
        $h = new TextHelper($this->container);

        $this->assertEquals('1k', $h->bytes(1024));
        $this->assertEquals('33.71k', $h->bytes(34520));
    }

    public function testContains()
    {
        $h = new TextHelper($this->container);

        $this->assertTrue($h->contains('abc', 'b'));
        $this->assertFalse($h->contains('abc', 'd'));
    }

    public function testInList()
    {
        $h = new TextHelper($this->container);
        $this->assertEquals('?', $h->in('a', ['b' => 'c']));
        $this->assertEquals('c', $h->in('b', ['b' => 'c']));
    }
}
