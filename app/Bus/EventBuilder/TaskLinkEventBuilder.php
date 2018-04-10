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

use Jitamin\Bus\Event\TaskLinkEvent;
use Jitamin\Model\TaskLinkModel;

/**
 * Class TaskLinkEventBuilder.
 */
class TaskLinkEventBuilder extends BaseEventBuilder
{
    protected $taskLinkId = 0;

    /**
     * Set taskLinkId.
     *
     * @param int $taskLinkId
     *
     * @return $this
     */
    public function withTaskLinkId($taskLinkId)
    {
        $this->taskLinkId = $taskLinkId;

        return $this;
    }

    /**
     * Build event data.
     *
     * @return TaskLinkEvent|null
     */
    public function buildEvent()
    {
        $taskLink = $this->taskLinkModel->getById($this->taskLinkId);

        if (empty($taskLink)) {
            $this->logger->debug(__METHOD__.': TaskLink not found');

            return;
        }

        return new TaskLinkEvent([
            'task_link' => $taskLink,
            'task'      => $this->taskFinderModel->getDetails($taskLink['task_id']),
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
        if ($eventName === TaskLinkModel::EVENT_CREATE_UPDATE) {
            return l('%s set a new internal link for the task #%d', $author, $eventData['task']['id']);
        } elseif ($eventName === TaskLinkModel::EVENT_DELETE) {
            return l('%s removed an internal link for the task #%d', $author, $eventData['task']['id']);
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
        if ($eventName === TaskLinkModel::EVENT_CREATE_UPDATE) {
            return l('A new internal link for the task #%d have been defined', $eventData['task']['id']);
        } elseif ($eventName === TaskLinkModel::EVENT_DELETE) {
            return l('Internal link removed for the task #%d', $eventData['task']['id']);
        }

        return '';
    }
}
