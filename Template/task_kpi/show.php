<section id="main">
	<?= $this->projectHeader->render($project, 'KPIController', 'show', false, 'KPI') ?>
			
	<?php if (empty($metrics)): ?>
		<p class="alert"><?= t('Not enough metrics data to show the graph.') ?></p>
	<?php else: ?>
		<div id="KPI-hours">
			<?= $this->app->component('chart-project-KPI-hours', array(
				'metrics' => $metrics,
			)) ?>
		</div>	
			<table class="table-striped">
				<tr>
					<th><?= t('Column') ?></th>
					<th><?= t('Number of tasks') ?></th>
					<th><?= t('Percentage') ?></th>
					<th><?= t('Hours Used') ?></th>
					<th><?= t('Hours Planned') ?></th>
				</tr>
				<?php foreach ($metrics as $metric): ?>
				<tr>
					<td>
						<?= $this->text->e($metric['column_title']) ?>
					</td>
					<td>
						<?= $metric['nb_tasks'] ?>
					</td>
					<td>
						<?= n($metric['nb_hrs_percent']) ?>%
					</td>
					<td>
						<?= $metric['nb_hrs_used'] ?>
					</td>
					<td>
						<?= $metric['nb_hrs_plan'] ?>
					</td>
				</tr>
			<?php endforeach ?>
			</table>
		

		<div id="KPI-tasks">
			<?= $this->app->component('chart-project-KPI-tasks', array(
				'metrics' => $metrics,
			)) ?>
		</div>

			<table class="table-striped">
				<tr>
					<th><?= t('Column') ?></th>
					<th><?= t('Number of Escalations') ?></th>
					<th><?= t('Percentage') ?></th>
					<th><?= t('Tasks Completed') ?></th>
					<th><?= t('Tasks Planned') ?></th>
				</tr>
				<?php foreach ($metrics as $metric): ?>
				<tr>
					<td>
						<?= $this->text->e($metric['column_title']) ?>
					</td>
					<td>
						<?= $metric['nb_escalations'] ?>
					</td>
					<td>
						<?= n($metric['nb_tasks_percent']) ?>%
					</td>
					<td>
						<?= $metric['nb_tasks_complete'] ?>
					</td>
					<td>
						<?= $metric['nb_tasks'] ?>
					</td>
				</tr>
				<?php endforeach ?>

			</table>

	<?php endif ?>	
    
</section>
