<section class="accordion-section <?= empty($links) ? 'accordion-collapsed' : '' ?>">
    <div class="accordion-title">
        <h3><a href="#" class="fa accordion-toggle"></a> <?= t('External links') ?></h3>
    </div>
    <div class="accordion-content">
        <?= $this->render('task/external_link/table', [
            'links'   => $links,
            'task'    => $task,
            'project' => $project,
        ]) ?>
    </div>
</section>
