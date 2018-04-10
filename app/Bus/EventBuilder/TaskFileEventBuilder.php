<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Bus\EventBuilder;

use Jitamin\Bus\Event\GenericEvent;
use Jitamin\Bus\Event\TaskFileEvent;
use Jitamin\Model\TaskFileModel;

/**
 * Class TaskFileEventBuilder.
 */
class TaskFileEventBuilder extends BaseEventBuilder
{
    protected $fileId = 0;

    /**
     * Set fileId.
     *
     * @param int $fileId
     *
     * @return $this
     */
    public function withFileId($fileId)
    {
        $this->fileId = $fileId;

        return $this;
    }

    /**
     * Build event data.
     *
     * @return GenericEvent|null
     */
    public function buildEvent()
    {
        $file = $this->taskFileModel->getById($this->fileId);

        if (empty($file)) {
            $this->logger->debug(__METHOD__.': File not found');

            return;
        }

        return new TaskFileEvent([
            'file' => $file,
            'task' => $this->taskFinderModel->getDetails($file['task_id']),
        ]);
    }

    /**
     * Get event title with author.
     *
     * @param string $author
     * @param string $eventName
     * @param array  $eventData
     *
     * @return string
     */
    public function buildTitleWithAuthor($author, $eventName, array $eventData)
    {
        if ($eventName === TaskFileModel::EVENT_CREATE) {
            return l('%s attached a file to the task #%d', $author, $eventData['task']['id']);
        }

        return '';
    }

    /**
     * Get event title without author.
     *
     * @param string $eventName
     * @param array  $eventData
     *
     * @return string
     */
    public function buildTitleWithoutAuthor($eventName, array $eventData)
    {
        if ($eventName === TaskFileModel::EVENT_CREATE) {
            return l('New attachment on task #%d: %s', $eventData['file']['task_id'], $eventData['file']['name']);
        }

        return '';
    }
}
