<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Http\Controllers\Profile;

use Jitamin\Filter\UserNameFilter;
use Jitamin\Formatter\UserAutoCompleteFormatter;
use Jitamin\Http\Controllers\Controller;
use Jitamin\Model\UserModel;

/**
 * User Ajax Controller.
 */
class UserAjaxController extends Controller
{
    /**
     * User auto-completion (Ajax).
     */
    public function autocomplete()
    {
        $search = $this->request->getStringParam('term');
        $filter = $this->userQuery->withFilter(new UserNameFilter($search));
        $filter->getQuery()->asc(UserModel::TABLE.'.name')->asc(UserModel::TABLE.'.username');
        $this->response->json($filter->format(new UserAutoCompleteFormatter($this->container)));
    }

    /**
     * User mention auto-completion (Ajax).
     */
    public function mention()
    {
        $project_id = $this->request->getStringParam('project_id');
        $query = $this->request->getStringParam('q');
        $users = $this->projectPermissionModel->findUsernames($project_id, $query);
        $this->response->json($users);
    }

    /**
     * Check if the user is connected.
     */
    public function status()
    {
        $this->response->text('OK');
    }
}
