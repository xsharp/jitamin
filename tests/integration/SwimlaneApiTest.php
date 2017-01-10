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

class SwimlaneApiTest extends BaseApiTest
{
    protected $projectName = 'My project to test swimlanes';
    private $swimlaneId = 0;

    public function testAll()
    {
        $this->assertCreateTeamProject();
    }

    public function assertGetDefaultSwimlane()
    {
        $swimlane = $this->app->getDefaultSwimlane($this->projectId);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals('Default swimlane', $swimlane['default_swimlane']);
    }

    public function assertAddSwimlane()
    {
        $this->swimlaneId = $this->app->addSwimlane($this->projectId, 'Swimlane 1');
        $this->assertNotFalse($this->swimlaneId);
        $this->assertNotFalse($this->app->addSwimlane($this->projectId, 'Swimlane 2'));
    }

    public function assertGetSwimlane()
    {
        $swimlane = $this->app->getSwimlane($this->swimlaneId);
        $this->assertInternalType('array', $swimlane);
        $this->assertEquals('Swimlane 1', $swimlane['name']);
    }

    public function assertUpdateSwimlane()
    {
        $this->assertTrue($this->app->updateSwimlane($this->swimlaneId, 'Another swimlane'));

        $swimlane = $this->app->getSwimlaneById($this->swimlaneId);
        $this->assertEquals('Another swimlane', $swimlane['name']);
    }

    public function assertDisableSwimlane()
    {
        $this->assertTrue($this->app->disableSwimlane($this->projectId, $this->swimlaneId));

        $swimlane = $this->app->getSwimlaneById($this->swimlaneId);
        $this->assertEquals(0, $swimlane['is_active']);
    }

    public function assertEnableSwimlane()
    {
        $this->assertTrue($this->app->enableSwimlane($this->projectId, $this->swimlaneId));

        $swimlane = $this->app->getSwimlaneById($this->swimlaneId);
        $this->assertEquals(1, $swimlane['is_active']);
    }

    public function assertGetAllSwimlanes()
    {
        $swimlanes = $this->app->getAllSwimlanes($this->projectId);
        $this->assertCount(2, $swimlanes);
        $this->assertEquals('Another swimlane', $swimlanes[0]['name']);
        $this->assertEquals('Swimlane 2', $swimlanes[1]['name']);
    }

    public function assertGetActiveSwimlane()
    {
        $this->assertTrue($this->app->disableSwimlane($this->projectId, $this->swimlaneId));

        $swimlanes = $this->app->getActiveSwimlanes($this->projectId);
        $this->assertCount(2, $swimlanes);
        $this->assertEquals('Default swimlane', $swimlanes[0]['name']);
        $this->assertEquals('Swimlane 2', $swimlanes[1]['name']);
    }

    public function assertRemoveSwimlane()
    {
        $this->assertTrue($this->app->removeSwimlane($this->projectId, $this->swimlaneId));
    }

    public function assertChangePosition()
    {
        $swimlaneId1 = $this->app->addSwimlane($this->projectId, 'Swimlane A');
        $this->assertNotFalse($this->app->addSwimlane($this->projectId, 'Swimlane B'));

        $swimlanes = $this->app->getAllSwimlanes($this->projectId);
        $this->assertCount(3, $swimlanes);

        $this->assertTrue($this->app->changeSwimlanePosition($this->projectId, $swimlaneId1, 3));
    }
}
