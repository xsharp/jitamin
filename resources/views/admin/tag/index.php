<div class="page-header">
    <h2><?= t('Global tags') ?></h2>
</div>

<?php if (empty($tags)): ?>
    <p class="alert"><?= t('There is no global tag at the moment.') ?></p>
<?php else: ?>
    <table class="table-striped table-scrolling">
        <tr>
            <th class="column-80"><?= t('Tag') ?></th>
            <th><?= t('Action') ?></th>
        </tr>
        <?php foreach ($tags as $tag): ?>
            <tr>
                <td><?= $this->text->e($tag['name']) ?></td>
                <td>
                    <i class="fa fa-times" aria-hidden="true"></i>
                    <?= $this->url->link(t('Remove'), 'Admin/TagController', 'confirm', ['tag_id' => $tag['id']], false, 'popover') ?>
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                    <?= $this->url->link(t('Edit'), 'Admin/TagController', 'edit', ['tag_id' => $tag['id']], false, 'popover') ?>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>
