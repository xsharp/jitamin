<section class="public-board">

   <?= $this->render('project/board/table_container', [
            'project'                        => $project,
            'swimlanes'                      => $swimlanes,
            'board_private_refresh_interval' => $board_private_refresh_interval,
            'board_highlight_period'         => $board_highlight_period,
            'not_editable'                   => true,
    ]) ?>

</section>