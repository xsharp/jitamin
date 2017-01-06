<div class="page-header">
    <h2><?= t('Burndown chart') ?></h2>
</div>

<?php if (!$display_graph): ?>
    <p class="alert"><?= t('You need at least 2 days of data to show the chart.') ?></p>
<?php else: ?>
    <section id="analytic-burndown">
        <div id="chart" data-metrics='<?= json_encode($metrics, JSON_HEX_APOS) ?>' data-date-format="<?= e('%%Y-%%m-%%d') ?>" data-label-total="<?= t('Total for all columns') ?>"></div>
    </section>
<?php endif ?>

<hr/>

<form method="post" class="form-inline" action="<?= $this->url->href('Project/AnalyticController', 'burndown', ['project_id' => $project['id']]) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <div class="form-inline-group">
        <?= $this->form->date(t('Start date'), 'from', $values) ?>
    </div>

    <div class="form-inline-group">
        <?= $this->form->date(t('End date'), 'to', $values) ?>
    </div>

    <div class="form-inline-group">
        <button type="submit" class="btn btn-info"><?= t('Execute') ?></button>
    </div>
</form>

<p class="alert alert-info"><?= t('This chart show the task complexity over the time (Work Remaining).') ?></p>
