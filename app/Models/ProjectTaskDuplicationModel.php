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

use Jitamin\Foundation\Database\Model;

/**
 * Project Task Duplication Model.
 */
class ProjectTaskDuplicationModel extends Model
{
    /**
     * Duplicate all tasks to another project.
     *
     * @param int $src_project_id
     * @param int $dst_project_id
     *
     * @return bool
     */
    public function duplicate($src_project_id, $dst_project_id)
    {
        $task_ids = $this->taskFinderModel->getAllIds($src_project_id, [TaskModel::STATUS_OPEN, TaskModel::STATUS_CLOSED]);

        foreach ($task_ids as $task_id) {
            if (!$this->taskProjectDuplicationModel->duplicateToProject($task_id, $dst_project_id)) {
                return false;
            }
        }

        return true;
    }
}
