<div class="page-header">
    <h2><?= t('Mark a task as moved') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to blah blah the task "%s"?', $task['title']) ?>
    </p>

    <?= $this->modal->confirmButtons(
        'KPIController',
        'editmoved',
        array('plugin' => 'KPI', 'task_id' => $task['id'], 'project_id' => $task['project_id'], 'confirmation' => 'yes')
    ) ?>
</div>
