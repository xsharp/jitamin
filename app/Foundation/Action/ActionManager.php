<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Foundation\Action;

use Jitamin\Action\Base as ActionBase;
use Jitamin\Foundation\Base;
use RuntimeException;

/**
 * Action Manager.
 */
class ActionManager extends Base
{
    /**
     * List of automatic actions.
     *
     * @var ActionBase[]
     */
    private $actions = [];

    /**
     * Register a new automatic action.
     *
     * @param ActionBase $action
     *
     * @return ActionManager
     */
    public function register(ActionBase $action)
    {
        $this->actions[$action->getName()] = $action;

        return $this;
    }

    /**
     * Get automatic action instance.
     *
     * @param string $name Absolute class name with namespace
     *
     * @return ActionBase
     */
    public function getAction($name)
    {
        if (isset($this->actions[$name])) {
            return $this->actions[$name];
        }

        throw new RuntimeException('Automatic Action Not Found: '.$name);
    }

    /**
     * Get available automatic actions.
     *
     * @return array
     */
    public function getAvailableActions()
    {
        $actions = [];

        foreach ($this->actions as $action) {
            if (count($action->getEvents()) > 0) {
                $actions[$action->getName()] = $action->getDescription();
            }
        }

        asort($actions);

        return $actions;
    }

    /**
     * Get all available action parameters.
     *
     * @param array $actions
     *
     * @return array
     */
    public function getAvailableParameters(array $actions)
    {
        $params = [];

        foreach ($actions as $action) {
            $currentAction = $this->getAction($action['action_name']);
            $params[$currentAction->getName()] = $currentAction->getActionRequiredParameters();
        }

        return $params;
    }

    /**
     * Get list of compatible events for a given action.
     *
     * @param string $name
     *
     * @return array
     */
    public function getCompatibleEvents($name)
    {
        $events = [];
        $actionEvents = $this->getAction($name)->getEvents();

        foreach ($this->eventManager->getAll() as $event => $description) {
            if (in_array($event, $actionEvents)) {
                $events[$event] = $description;
            }
        }

        return $events;
    }

    /**
     * Bind automatic actions to events.
     *
     * @return ActionManager
     */
    public function attachEvents()
    {
        if ($this->userSession->isLogged()) {
            $actions = $this->actionModel->getAllByUser($this->userSession->getId());
        } else {
            $actions = $this->actionModel->getAll();
        }

        foreach ($actions as $action) {
            $listener = clone $this->getAction($action['action_name']);
            $listener->setProjectId($action['project_id']);

            foreach ($action['params'] as $param_name => $param_value) {
                $listener->setParam($param_name, $param_value);
            }

            $this->dispatcher->addListener($action['event_name'], [$listener, 'execute']);
        }

        return $this;
    }

    /**
     * Remove all listeners for automated actions.
     */
    public function removeEvents()
    {
        foreach ($this->dispatcher->getListeners() as $eventName => $listeners) {
            foreach ($listeners as $listener) {
                if (is_array($listener) && $listener[0] instanceof ActionBase) {
                    $this->dispatcher->removeListener($eventName, $listener);
                }
            }
        }
    }
}
