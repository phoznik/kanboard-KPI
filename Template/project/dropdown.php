<?php if ($this->user->hasProjectAccess('KPIController', 'show', $project['id'])): ?>
    <li>
        <?= $this->url->icon('line-chart', t('KPI'), 'KPIController', 'show', array('project_id' => $project['id'], 'plugin' => 'KPI')) ?>
    </li>
<?php endif ?>