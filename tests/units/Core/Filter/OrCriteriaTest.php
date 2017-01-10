<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Jitamin\Filter\TaskAssigneeFilter;
use Jitamin\Filter\TaskTitleFilter;
use Jitamin\Foundation\Filter\OrCriteria;
use Jitamin\Model\ProjectModel;
use Jitamin\Model\TaskFinderModel;
use Jitamin\Model\TaskModel;
use Jitamin\Model\UserModel;

require_once __DIR__.'/../../Base.php';

class OrCriteriaTest extends Base
{
    public function testWithSameFilter()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskModel = new TaskModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $userModel = new UserModel($this->container);
        $query = $taskFinder->getExtendedQuery();

        $this->assertEquals(2, $userModel->create(['username' => 'foobar', 'email' => 'foobar@here', 'name' => 'Foo Bar']));
        $this->assertEquals(1, $projectModel->create(['name' => 'Test']));
        $this->assertEquals(1, $taskModel->create(['title' => 'Test 1', 'project_id' => 1, 'owner_id' => 2]));
        $this->assertEquals(2, $taskModel->create(['title' => 'Test 2', 'project_id' => 1, 'owner_id' => 1]));
        $this->assertEquals(3, $taskModel->create(['title' => 'Test 3', 'project_id' => 1, 'owner_id' => 0]));

        $criteria = new OrCriteria();
        $criteria->withQuery($query);
        $criteria->withFilter(TaskAssigneeFilter::getInstance(1));
        $criteria->withFilter(TaskAssigneeFilter::getInstance(2));
        $criteria->apply();

        $this->assertCount(2, $query->findAll());
    }

    public function testWithDifferentFilter()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskModel = new TaskModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $userModel = new UserModel($this->container);
        $query = $taskFinder->getExtendedQuery();

        $this->assertEquals(2, $userModel->create(['username' => 'foobar', 'email' => 'foobar@foobar', 'name' => 'Foo Bar']));
        $this->assertEquals(1, $projectModel->create(['name' => 'Test']));
        $this->assertEquals(1, $taskModel->create(['title' => 'ABC', 'project_id' => 1, 'owner_id' => 2]));
        $this->assertEquals(2, $taskModel->create(['title' => 'DEF', 'project_id' => 1, 'owner_id' => 1]));

        $criteria = new OrCriteria();
        $criteria->withQuery($query);
        $criteria->withFilter(TaskAssigneeFilter::getInstance(1));
        $criteria->withFilter(TaskTitleFilter::getInstance('ABC'));
        $criteria->apply();

        $this->assertCount(2, $query->findAll());
    }
}
