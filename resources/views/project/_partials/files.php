<?php if (!empty($files)): ?>
    <table class="table-striped table-scrolling">
        <tr>
            <th><?= t('Filename') ?></th>
            <th><?= t('Creator') ?></th>
            <th><?= t('Date') ?></th>
            <th><?= t('Size') ?></th>
        </tr>
        <?php foreach ($files as $file): ?>
            <tr>
                <td>
                    <i class="fa <?= $this->file->icon($file['name']) ?> fa-fw"></i>
                    <div class="dropdown">
                        <a href="#" class="dropdown-menu dropdown-menu-link-text"><?= $this->text->e($file['name']) ?> <i class="fa fa-caret-down"></i></a>
                        <ul>
                            <?php if ($this->file->getPreviewType($file['name']) !== null): ?>
                                <li>
                                    <i class="fa fa-eye fa-fw"></i>
                                    <?= $this->url->link(t('View file'), 'AttachmentController', 'show', ['project_id' => $project['id'], 'file_id' => $file['id']], false, 'popover') ?>
                                </li>
                            <?php elseif ($this->file->getBrowserViewType($file['name']) !== null): ?>
                                <li>
                                    <i class="fa fa-eye fa-fw"></i>
                                    <?= $this->url->link(t('View file'), 'AttachmentController', 'browser', ['project_id' => $project['id'], 'file_id' => $file['id']], false, '', '', true) ?>
                                </li>
                            <?php endif ?>
                            <li>
                                <i class="fa fa-download fa-fw"></i>
                                <?= $this->url->link(t('Download'), 'AttachmentController', 'download', ['project_id' => $project['id'], 'file_id' => $file['id']]) ?>
                            </li>
                            <?php if ($this->user->hasProjectAccess('Project/ProjectFileController', 'remove', $project['id'])): ?>
                                <li>
                                    <i class="fa fa-trash fa-fw"></i>
                                    <?= $this->url->link(t('Remove'), 'Project/ProjectFileController', 'remove', ['project_id' => $project['id'], 'file_id' => $file['id']], false, 'popover') ?>
                                </li>
                            <?php endif ?>
                        </ul>
                    </div>
                </td>
                <td>
                    <?= $this->text->e($file['user_name'] ?: $file['username']) ?>
                </td>
                <td>
                    <?= $this->dt->date($file['date']) ?>
                </td>
                <td>
                    <?= $this->text->bytes($file['size']) ?>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>
