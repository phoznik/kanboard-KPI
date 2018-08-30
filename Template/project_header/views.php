<?php if ($this->user->hasProjectAccess('KPIController', 'show', $project['id'])): ?>
<li <?= $this->app->checkMenuSelection('KPIController') ?>>
<?= $this->url->icon('line-chart', t('KPI'), 'KPIController', 'show', array('project_id' => $project['id'], 'search' => $filters['search'], 'plugin' => 'KPI'), false, 'view-KPI', t('Linak KPI')) ?>
</li>
<?php endif ?>