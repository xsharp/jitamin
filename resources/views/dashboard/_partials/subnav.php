<div class="page-header">
    <ul class="nav nav-tabs">
        <li <?= $this->app->setActive('Dashboard/ProjectController', 'index', 'dashboard') ?>>
            <?= $this->url->link(t('My projects'), 'Dashboard/ProjectController', 'index') ?>
        </li>
        <li <?= $this->app->setActive('Dashboard/ProjectController', 'starred', 'dashboard') ?>>
            <?= $this->url->link(t('Starred projects'), 'Dashboard/ProjectController', 'starred') ?>
        </li>
        <li <?= $this->app->setActive('Dashboard/DashboardController', 'tasks', 'dashboard') ?>>
            <?= $this->url->link(t('My tasks'), 'Dashboard/DashboardController', 'tasks') ?>
        </li>
        <li <?= $this->app->setActive('Dashboard/DashboardController', 'subtasks', 'dashboard') ?>>
            <?= $this->url->link(t('My subtasks'), 'Dashboard/DashboardController', 'subtasks') ?>
        </li>
        <li <?= $this->app->setActive('Dashboard/DashboardController', 'calendar', 'dashboard') ?>>
            <?= $this->url->link(t('My calendar'), 'Dashboard/DashboardController', 'calendar') ?>
        </li>
        <li <?= $this->app->setActive('Dashboard/DashboardController', 'activities', 'dashboard') ?>>
            <?= $this->url->link(t('Project activities'), 'Dashboard/DashboardController', 'activities') ?>
        </li>
        <?= $this->hook->render('template:dashboard:subside') ?>
    </ul>
</div>