<?= $this->projectHeader->render($project) ?>
<div id="calendar"
     data-store-url="<?= $this->url->href('CalendarController', 'store', ['project_id' => $project['id']]) ?>"
     data-check-url="<?= $this->url->href('CalendarController', 'project', ['project_id' => $project['id']]) ?>"
     data-check-interval="<?= $check_interval ?>"
>
</div>
