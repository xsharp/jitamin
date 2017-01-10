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

class ProjectApiTest extends BaseApiTest
{
    protected $projectName = 'My team project';

    public function testAll()
    {
        $this->assertCreateTeamProject();
        $this->assertGetProjectById();
        $this->assertGetProjectByName();
        $this->assertGetAllProjects();
        $this->assertUpdateProject();
        $this->assertUpdateProjectIdentifier();
        $this->assertCreateProjectWithIdentifier();
        $this->assertGetProjectActivity();
        $this->assertGetProjectsActivity();
        $this->assertEnableDisableProject();
        $this->assertEnableDisablePublicAccess();
        $this->assertRemoveProject();
    }

    public function assertGetProjectById()
    {
        $project = $this->app->getProjectById($this->projectId);
        $this->assertNotNull($project);
        $this->assertEquals($this->projectName, $project['name']);
        $this->assertEquals('Description', $project['description']);
    }

    public function assertGetProjectByName()
    {
        $project = $this->app->getProjectByName($this->projectName);
        $this->assertNotNull($project);
        $this->assertEquals($this->projectId, $project['id']);
        $this->assertEquals($this->projectName, $project['name']);
        $this->assertEquals('Description', $project['description']);
    }

    public function assertGetAllProjects()
    {
        $projects = $this->app->getAllProjects();
        $this->assertNotEmpty($projects);
    }

    public function assertGetProjectActivity()
    {
        $activities = $this->app->getProjectActivity($this->projectId);
        $this->assertInternalType('array', $activities);
        $this->assertCount(0, $activities);
    }

    public function assertGetProjectsActivity()
    {
        $activities = $this->app->getProjectActivities(['project_ids' => [$this->projectId]]);
        $this->assertInternalType('array', $activities);
        $this->assertCount(0, $activities);
    }

    public function assertUpdateProject()
    {
        $this->assertTrue($this->app->updateProject(['project_id' => $this->projectId, 'name' => 'test', 'description' => 'test']));

        $project = $this->app->getProjectById($this->projectId);
        $this->assertNotNull($project);
        $this->assertEquals('test', $project['name']);
        $this->assertEquals('test', $project['description']);

        $this->assertTrue($this->app->updateProject(['project_id' => $this->projectId, 'name' => $this->projectName]));
    }

    public function assertUpdateProjectIdentifier()
    {
        $this->assertTrue($this->app->updateProject([
            'project_id' => $this->projectId,
            'identifier' => 'MYPROJECT',
        ]));

        $project = $this->app->getProjectById($this->projectId);
        $this->assertNotNull($project);
        $this->assertEquals($this->projectName, $project['name']);
        $this->assertEquals('MYPROJECT', $project['identifier']);
    }

    public function assertCreateProjectWithIdentifier()
    {
        $projectId = $this->app->createProject([
            'name'       => 'My project with an identifier',
            'identifier' => 'MYPROJECTWITHIDENTIFIER',
        ]);

        $this->assertNotFalse($projectId);

        $project = $this->app->getProjectByIdentifier('MYPROJECTWITHIDENTIFIER');
        $this->assertEquals($projectId, $project['id']);
        $this->assertEquals('My project with an identifier', $project['name']);
        $this->assertEquals('MYPROJECTWITHIDENTIFIER', $project['identifier']);
    }

    public function assertEnableDisableProject()
    {
        $this->assertTrue($this->app->disableProject($this->projectId));
        $this->assertTrue($this->app->enableProject($this->projectId));
    }

    public function assertEnableDisablePublicAccess()
    {
        $this->assertTrue($this->app->disableProjectPublicAccess($this->projectId));
        $this->assertTrue($this->app->enableProjectPublicAccess($this->projectId));
    }

    public function assertRemoveProject()
    {
        $this->assertTrue($this->app->removeProject($this->projectId));
        $this->assertNull($this->app->getProjectById($this->projectId));
    }
}
