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

class CategoryApiTest extends BaseApiTest
{
    protected $projectName = 'My project to test categories';
    private $categoryId = 0;

    public function testAll()
    {
        $this->assertCreateTeamProject();
        $this->assertCreateCategory();
        $this->assertThatCategoriesAreUnique();
        $this->assertGetCategory();
        $this->assertGetAllCategories();
        $this->assertCategoryUpdate();
        $this->assertRemoveCategory();
    }

    public function assertCreateCategory()
    {
        $this->categoryId = $this->app->createCategory([
            'name'       => 'Category',
            'project_id' => $this->projectId,
        ]);

        $this->assertNotFalse($this->categoryId);
    }

    public function assertThatCategoriesAreUnique()
    {
        $this->assertFalse($this->app->execute('createCategory', [
            'name'       => 'Category',
            'project_id' => $this->projectId,
        ]));
    }

    public function assertGetCategory()
    {
        $category = $this->app->getCategory($this->categoryId);

        $this->assertInternalType('array', $category);
        $this->assertEquals($this->categoryId, $category['id']);
        $this->assertEquals('Category', $category['name']);
        $this->assertEquals($this->projectId, $category['project_id']);
    }

    public function assertGetAllCategories()
    {
        $categories = $this->app->getAllCategories($this->projectId);

        $this->assertCount(1, $categories);
        $this->assertEquals($this->categoryId, $categories[0]['id']);
        $this->assertEquals('Category', $categories[0]['name']);
        $this->assertEquals($this->projectId, $categories[0]['project_id']);
    }

    public function assertCategoryUpdate()
    {
        $this->assertTrue($this->app->execute('updateCategory', [
            'id'   => $this->categoryId,
            'name' => 'Renamed category',
        ]));

        $category = $this->app->getCategory($this->categoryId);
        $this->assertEquals('Renamed category', $category['name']);
    }

    public function assertRemoveCategory()
    {
        $this->assertTrue($this->app->removeCategory($this->categoryId));
        $this->assertFalse($this->app->removeCategory($this->categoryId));
        $this->assertFalse($this->app->removeCategory(1111));
    }
}
