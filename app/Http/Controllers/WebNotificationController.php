<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Controller;

/**
 * Web notification controller.
 */
class WebNotificationController extends BaseController
{
    /**
     * Mark all notifications as read.
     */
    public function flush()
    {
        $user_id = $this->getUserId();

        $this->userUnreadNotificationModel->markAllAsRead($user_id);
        $this->response->redirect($this->helper->url->to('Dashboard/DashboardController', 'notifications', ['user_id' => $user_id]));
    }

    /**
     * Mark a notification as read.
     */
    public function remove()
    {
        $user_id = $this->getUserId();
        $notification_id = $this->request->getIntegerParam('notification_id');

        $this->userUnreadNotificationModel->markAsRead($user_id, $notification_id);
        $this->response->redirect($this->helper->url->to('Dashboard/DashboardController', 'notifications', ['user_id' => $user_id]));
    }

    /**
     * Redirect to the task and mark notification as read.
     */
    public function redirect()
    {
        $user_id = $this->getUserId();
        $notification_id = $this->request->getIntegerParam('notification_id');

        $notification = $this->userUnreadNotificationModel->getById($notification_id);
        $this->userUnreadNotificationModel->markAsRead($user_id, $notification_id);

        if (empty($notification)) {
            $this->response->redirect($this->helper->url->to('Dashboard/DashboardController', 'notifications', ['user_id' => $user_id]));
        } elseif ($this->helper->text->contains($notification['event_name'], 'comment')) {
            $this->response->redirect($this->helper->url->to(
                'Task/TaskController',
                'show',
                ['task_id' => $this->notificationModel->getTaskIdFromEvent($notification['event_name'], $notification['event_data'])],
                'comment-'.$notification['event_data']['comment']['id']
            ));
        } else {
            $this->response->redirect($this->helper->url->to(
                'Task/TaskController',
                'show',
                ['task_id' => $this->notificationModel->getTaskIdFromEvent($notification['event_name'], $notification['event_data'])]
            ));
        }
    }

    /**
     * Get user id.
     */
    protected function getUserId()
    {
        $user_id = $this->request->getIntegerParam('user_id');

        if (!$this->userSession->isAdmin() && $user_id != $this->userSession->getId()) {
            $user_id = $this->userSession->getId();
        }

        return $user_id;
    }
}
