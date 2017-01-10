<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Console;

use Jitamin\Foundation\Security\Role;
use Jitamin\Model\TaskModel;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Task overdue notification command class.
 */
class TaskOverdueNotificationCommand extends BaseCommand
{
    /**
     * Configure the console command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('notification:overdue-tasks')
            ->setDescription('Send notifications for overdue tasks')
            ->addOption('show', null, InputOption::VALUE_NONE, 'Show sent overdue tasks')
            ->addOption('group', null, InputOption::VALUE_NONE, 'Group all overdue tasks for one user (from all projects) in one email')
            ->addOption('manager', null, InputOption::VALUE_NONE, 'Send all overdue tasks to project manager(s) in one email');
    }

    /**
     * Execute the console command.
     *
     * @param InputInterface  $output
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('group')) {
            $tasks = $this->sendGroupOverdueTaskNotifications();
        } elseif ($input->getOption('manager')) {
            $tasks = $this->sendOverdueTaskNotificationsToManagers();
        } else {
            $tasks = $this->sendOverdueTaskNotifications();
        }

        if ($input->getOption('show')) {
            $this->showTable($output, $tasks);
        }
    }

    /**
     * Show table.
     *
     * @param OutputInterface $output
     * @param array           $tasks
     *
     * @return void
     */
    public function showTable(OutputInterface $output, array $tasks)
    {
        $rows = [];

        foreach ($tasks as $task) {
            $rows[] = [
                $task['id'],
                $task['title'],
                date('Y-m-d', $task['date_due']),
                $task['project_id'],
                $task['project_name'],
                $task['assignee_name'] ?: $task['assignee_username'],
            ];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['Id', 'Title', 'Due date', 'Project Id', 'Project name', 'Assignee'])
            ->setRows($rows)
            ->render();
    }

    /**
     * Send all overdue tasks for one user in one email.
     */
    public function sendGroupOverdueTaskNotifications()
    {
        $tasks = $this->taskFinderModel->getOverdueTasks();

        foreach ($this->groupByColumn($tasks, 'owner_id') as $user_tasks) {
            $users = $this->userNotificationModel->getUsersWithNotificationEnabled($user_tasks[0]['project_id']);

            foreach ($users as $user) {
                $this->sendUserOverdueTaskNotifications($user, $user_tasks);
            }
        }

        return $tasks;
    }

    /**
     * Send all overdue tasks in one email to project manager(s).
     */
    public function sendOverdueTaskNotificationsToManagers()
    {
        $tasks = $this->taskFinderModel->getOverdueTasks();

        foreach ($this->groupByColumn($tasks, 'project_id') as $project_id => $project_tasks) {
            $users = $this->userNotificationModel->getUsersWithNotificationEnabled($project_id);
            $managers = [];

            foreach ($users as $user) {
                $role = $this->projectUserRoleModel->getUserRole($project_id, $user['id']);
                if ($role == Role::PROJECT_MANAGER) {
                    $managers[] = $user;
                }
            }

            foreach ($managers as $manager) {
                $this->sendUserOverdueTaskNotificationsToManagers($manager, $project_tasks);
            }
        }

        return $tasks;
    }

    /**
     * Send overdue tasks.
     */
    public function sendOverdueTaskNotifications()
    {
        $tasks = $this->taskFinderModel->getOverdueTasks();

        foreach ($this->groupByColumn($tasks, 'project_id') as $project_id => $project_tasks) {
            $users = $this->userNotificationModel->getUsersWithNotificationEnabled($project_id);

            foreach ($users as $user) {
                $this->sendUserOverdueTaskNotifications($user, $project_tasks);
            }
        }

        return $tasks;
    }

    /**
     * Send overdue tasks for a given user.
     *
     * @param array $user
     * @param array $tasks
     */
    public function sendUserOverdueTaskNotifications(array $user, array $tasks)
    {
        $user_tasks = [];
        $project_names = [];

        foreach ($tasks as $task) {
            if ($this->userNotificationFilterModel->shouldReceiveNotification($user, ['task' => $task])) {
                $user_tasks[] = $task;
                $project_names[$task['project_id']] = $task['project_name'];
            }
        }

        if (!empty($user_tasks)) {
            $this->userNotificationModel->sendUserNotification(
                $user,
                TaskModel::EVENT_OVERDUE,
                ['tasks' => $user_tasks, 'project_name' => implode(', ', $project_names)]
            );
        }
    }

    /**
     * Send overdue tasks for a project manager(s).
     *
     * @param array $manager
     * @param array $tasks
     */
    public function sendUserOverdueTaskNotificationsToManagers(array $manager, array $tasks)
    {
        $this->userNotificationModel->sendUserNotification(
            $manager,
            TaskModel::EVENT_OVERDUE,
            ['tasks' => $tasks, 'project_name' => $tasks[0]['project_name']]
        );
    }

    /**
     * Group a collection of records by a column.
     *
     * @param array  $collection
     * @param string $column
     *
     * @return array
     */
    public function groupByColumn(array $collection, $column)
    {
        $result = [];

        foreach ($collection as $item) {
            $result[$item[$column]][] = $item;
        }

        return $result;
    }
}
