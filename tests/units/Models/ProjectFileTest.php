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

use Jitamin\Model\ProjectFileModel;
use Jitamin\Model\ProjectModel;

class ProjectFileTest extends Base
{
    public function testCreation()
    {
        $projectModel = new ProjectModel($this->container);
        $fileModel = new ProjectFileModel($this->container);

        $this->assertEquals(1, $projectModel->create(['name' => 'test']));
        $this->assertEquals(1, $fileModel->create(1, 'test', '/tmp/foo', 10));

        $file = $fileModel->getById(1);
        $this->assertEquals('test', $file['name']);
        $this->assertEquals('/tmp/foo', $file['path']);
        $this->assertEquals(0, $file['is_image']);
        $this->assertEquals(1, $file['project_id']);
        $this->assertEquals(time(), $file['date'], '', 2);
        $this->assertEquals(0, $file['user_id']);
        $this->assertEquals(10, $file['size']);

        $this->assertEquals(2, $fileModel->create(1, 'test2.png', '/tmp/foobar', 10));

        $file = $fileModel->getById(2);
        $this->assertEquals('test2.png', $file['name']);
        $this->assertEquals('/tmp/foobar', $file['path']);
        $this->assertEquals(1, $file['is_image']);
    }

    public function testCreationWithFileNameTooLong()
    {
        $projectModel = new ProjectModel($this->container);
        $fileModel = new ProjectFileModel($this->container);

        $this->assertEquals(1, $projectModel->create(['name' => 'test']));

        $this->assertNotFalse($fileModel->create(1, 'test', '/tmp/foo', 10));
        $this->assertNotFalse($fileModel->create(1, str_repeat('a', 1000), '/tmp/foo', 10));

        $files = $fileModel->getAll(1);
        $this->assertNotEmpty($files);
        $this->assertCount(2, $files);

        $this->assertEquals(str_repeat('a', 255), $files[0]['name']);
        $this->assertEquals('test', $files[1]['name']);
    }

    public function testCreationWithSessionOpen()
    {
        $this->container['sessionStorage']->user = ['id' => 1];

        $projectModel = new ProjectModel($this->container);
        $fileModel = new ProjectFileModel($this->container);

        $this->assertEquals(1, $projectModel->create(['name' => 'test']));
        $this->assertEquals(1, $fileModel->create(1, 'test', '/tmp/foo', 10));

        $file = $fileModel->getById(1);
        $this->assertEquals('test', $file['name']);
        $this->assertEquals(1, $file['user_id']);
    }

    public function testGetAll()
    {
        $projectModel = new ProjectModel($this->container);
        $fileModel = new ProjectFileModel($this->container);

        $this->assertEquals(1, $projectModel->create(['name' => 'test']));

        $this->assertEquals(1, $fileModel->create(1, 'B.pdf', '/tmp/foo', 10));
        $this->assertEquals(2, $fileModel->create(1, 'A.png', '/tmp/foo', 10));
        $this->assertEquals(3, $fileModel->create(1, 'D.doc', '/tmp/foo', 10));
        $this->assertEquals(4, $fileModel->create(1, 'C.jpg', '/tmp/foo', 10));

        $fileModeliles = $fileModel->getAll(1);
        $this->assertNotEmpty($fileModeliles);
        $this->assertCount(4, $fileModeliles);
        $this->assertEquals('C.jpg', $fileModeliles[0]['name']);
        $this->assertEquals('D.doc', $fileModeliles[1]['name']);
        $this->assertEquals('A.png', $fileModeliles[2]['name']);
        $this->assertEquals('B.pdf', $fileModeliles[3]['name']);

        $fileModeliles = $fileModel->getAllImages(1);
        $this->assertNotEmpty($fileModeliles);
        $this->assertCount(2, $fileModeliles);
        $this->assertEquals('C.jpg', $fileModeliles[0]['name']);
        $this->assertEquals('A.png', $fileModeliles[1]['name']);

        $fileModeliles = $fileModel->getAllDocuments(1);
        $this->assertNotEmpty($fileModeliles);
        $this->assertCount(2, $fileModeliles);
        $this->assertEquals('D.doc', $fileModeliles[0]['name']);
        $this->assertEquals('B.pdf', $fileModeliles[1]['name']);
    }

    public function testGetThumbnailPath()
    {
        $fileModel = new ProjectFileModel($this->container);
        $this->assertEquals('thumbnails'.DIRECTORY_SEPARATOR.'test', $fileModel->getThumbnailPath('test'));
    }

    public function testGeneratePath()
    {
        $fileModel = new ProjectFileModel($this->container);

        $this->assertStringStartsWith('projects'.DIRECTORY_SEPARATOR.'34'.DIRECTORY_SEPARATOR, $fileModel->generatePath(34, 'test.png'));
        $this->assertNotEquals($fileModel->generatePath(34, 'test1.png'), $fileModel->generatePath(34, 'test2.png'));
    }

    public function testUploadFiles()
    {
        $fileModel = $this
            ->getMockBuilder('\Jitamin\Model\ProjectFileModel')
            ->setConstructorArgs([$this->container])
            ->setMethods(['generateThumbnailFromFile'])
            ->getMock();

        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(['name' => 'test']));

        $files = [
            'name' => [
                'file1.png',
                'file2.doc',
            ],
            'tmp_name' => [
                '/tmp/phpYzdqkD',
                '/tmp/phpeEwEWG',
            ],
            'error' => [
                UPLOAD_ERR_OK,
                UPLOAD_ERR_OK,
            ],
            'size' => [
                123,
                456,
            ],
        ];

        $fileModel
            ->expects($this->once())
            ->method('generateThumbnailFromFile');

        $this->container['objectStorage']
            ->expects($this->at(0))
            ->method('moveUploadedFile')
            ->with($this->equalTo('/tmp/phpYzdqkD'), $this->anything());

        $this->container['objectStorage']
            ->expects($this->at(1))
            ->method('moveUploadedFile')
            ->with($this->equalTo('/tmp/phpeEwEWG'), $this->anything());

        $this->assertTrue($fileModel->uploadFiles(1, $files));

        $files = $fileModel->getAll(1);
        $this->assertCount(2, $files);

        $this->assertEquals(2, $files[0]['id']);
        $this->assertEquals('file2.doc', $files[0]['name']);
        $this->assertEquals(0, $files[0]['is_image']);
        $this->assertEquals(1, $files[0]['project_id']);
        $this->assertEquals(0, $files[0]['user_id']);
        $this->assertEquals(456, $files[0]['size']);
        $this->assertEquals(time(), $files[0]['date'], '', 2);

        $this->assertEquals(1, $files[1]['id']);
        $this->assertEquals('file1.png', $files[1]['name']);
        $this->assertEquals(1, $files[1]['is_image']);
        $this->assertEquals(1, $files[1]['project_id']);
        $this->assertEquals(0, $files[1]['user_id']);
        $this->assertEquals(123, $files[1]['size']);
        $this->assertEquals(time(), $files[1]['date'], '', 2);
    }

    public function testUploadFilesWithEmptyFiles()
    {
        $fileModel = new ProjectFileModel($this->container);
        $this->assertFalse($fileModel->uploadFiles(1, []));
    }

    public function testUploadFilesWithUploadError()
    {
        $files = [
            'name' => [
                'file1.png',
                'file2.doc',
            ],
            'tmp_name' => [
                '',
                '/tmp/phpeEwEWG',
            ],
            'error' => [
                UPLOAD_ERR_CANT_WRITE,
                UPLOAD_ERR_OK,
            ],
            'size' => [
                123,
                456,
            ],
        ];

        $fileModel = new ProjectFileModel($this->container);
        $this->assertFalse($fileModel->uploadFiles(1, $files));
    }

    public function testUploadFilesWithObjectStorageError()
    {
        $files = [
            'name' => [
                'file1.csv',
                'file2.doc',
            ],
            'tmp_name' => [
                '/tmp/phpYzdqkD',
                '/tmp/phpeEwEWG',
            ],
            'error' => [
                UPLOAD_ERR_OK,
                UPLOAD_ERR_OK,
            ],
            'size' => [
                123,
                456,
            ],
        ];

        $this->container['objectStorage']
            ->expects($this->at(0))
            ->method('moveUploadedFile')
            ->with($this->equalTo('/tmp/phpYzdqkD'), $this->anything())
            ->will($this->throwException(new \Jitamin\Foundation\ObjectStorage\ObjectStorageException('test')));

        $fileModel = new ProjectFileModel($this->container);
        $this->assertFalse($fileModel->uploadFiles(1, $files));
    }

    public function testUploadFileContent()
    {
        $fileModel = $this
            ->getMockBuilder('\Jitamin\Model\ProjectFileModel')
            ->setConstructorArgs([$this->container])
            ->setMethods(['generateThumbnailFromFile'])
            ->getMock();

        $projectModel = new ProjectModel($this->container);
        $data = 'test';

        $this->assertEquals(1, $projectModel->create(['name' => 'test']));

        $this->container['objectStorage']
            ->expects($this->once())
            ->method('put')
            ->with($this->anything(), $this->equalTo($data));

        $this->assertEquals(1, $fileModel->uploadContent(1, 'test.doc', base64_encode($data)));

        $files = $fileModel->getAll(1);
        $this->assertCount(1, $files);

        $this->assertEquals(1, $files[0]['id']);
        $this->assertEquals('test.doc', $files[0]['name']);
        $this->assertEquals(0, $files[0]['is_image']);
        $this->assertEquals(1, $files[0]['project_id']);
        $this->assertEquals(0, $files[0]['user_id']);
        $this->assertEquals(4, $files[0]['size']);
        $this->assertEquals(time(), $files[0]['date'], '', 2);
    }

    public function testUploadImageContent()
    {
        $fileModel = $this
            ->getMockBuilder('\Jitamin\Model\ProjectFileModel')
            ->setConstructorArgs([$this->container])
            ->setMethods(['generateThumbnailFromData'])
            ->getMock();

        $projectModel = new ProjectModel($this->container);
        $data = 'test';

        $this->assertEquals(1, $projectModel->create(['name' => 'test']));

        $fileModel
            ->expects($this->once())
            ->method('generateThumbnailFromData');

        $this->container['objectStorage']
            ->expects($this->once())
            ->method('put')
            ->with($this->anything(), $this->equalTo($data));

        $this->assertEquals(1, $fileModel->uploadContent(1, 'test.png', base64_encode($data)));

        $files = $fileModel->getAll(1);
        $this->assertCount(1, $files);

        $this->assertEquals(1, $files[0]['id']);
        $this->assertEquals('test.png', $files[0]['name']);
        $this->assertEquals(1, $files[0]['is_image']);
        $this->assertEquals(1, $files[0]['project_id']);
        $this->assertEquals(0, $files[0]['user_id']);
        $this->assertEquals(4, $files[0]['size']);
        $this->assertEquals(time(), $files[0]['date'], '', 2);
    }
}
