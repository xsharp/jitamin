<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Model;

use Jitamin\Bus\EventBuilder\CommentEventBuilder;
use Jitamin\Bus\EventBuilder\EventIteratorBuilder;
use Jitamin\Bus\EventBuilder\SubtaskEventBuilder;
use Jitamin\Bus\EventBuilder\TaskEventBuilder;
use Jitamin\Bus\EventBuilder\TaskFileEventBuilder;
use Jitamin\Bus\EventBuilder\TaskLinkEventBuilder;
use Jitamin\Foundation\Database\Model;

/**
 * Notification Model.
 */
class NotificationModel extends Model
{
    /**
     * Get the event title with author.
     *
     * @param string $eventAuthor
     * @param string $eventName
     * @param array  $eventData
     *
     * @return string
     */
    public function getTitleWithAuthor($eventAuthor, $eventName, array $eventData)
    {
        foreach ($this->getIteratorBuilder() as $builder) {
            $title = $builder->buildTitleWithAuthor($eventAuthor, $eventName, $eventData);

            if ($title !== '') {
                return $title;
            }
        }

        return e('Notification');
    }

    /**
     * Get the event title without author.
     *
     * @param string $eventName
     * @param array  $eventData
     *
     * @return string
     */
    public function getTitleWithoutAuthor($eventName, array $eventData)
    {
        foreach ($this->getIteratorBuilder() as $builder) {
            $title = $builder->buildTitleWithoutAuthor($eventName, $eventData);

            if ($title !== '') {
                return $title;
            }
        }

        return e('Notification');
    }

    /**
     * Get task id from event.
     *
     * @param string $eventName
     * @param array  $eventData
     *
     * @return int
     */
    public function getTaskIdFromEvent($eventName, array $eventData)
    {
        if ($eventName === TaskModel::EVENT_OVERDUE) {
            return $eventData['tasks'][0]['id'];
        }

        return isset($eventData['task']['id']) ? $eventData['task']['id'] : 0;
    }

    /**
     * Get iterator builder.
     *
     * @return EventIteratorBuilder
     */
    protected function getIteratorBuilder()
    {
        $iterator = new EventIteratorBuilder();
        $iterator
            ->withBuilder(TaskEventBuilder::getInstance($this->container))
            ->withBuilder(CommentEventBuilder::getInstance($this->container))
            ->withBuilder(SubtaskEventBuilder::getInstance($this->container))
            ->withBuilder(TaskFileEventBuilder::getInstance($this->container))
            ->withBuilder(TaskLinkEventBuilder::getInstance($this->container));

        return $iterator;
    }
}
