<?php /* if ($this->user->hasProjectAccess('TaskModificationController', 'edit', $task['project_id'])): ?>
	<?= $this->modal->large(
			'edit', 
			'', 
			'TaskModificationController',
			'edit',
			array(
				'task_id' => $task['id'], 
				'project_id' => $task['project_id']
			)
		) ?>
<?php endif */ ?>
<?php /* if ($this->projectRole->canRemoveTask($task)): ?>
	<?= $this->modal->confirm(
			'trash-o',
			'',
			'TaskSuppressionController', 
			'confirm', 
			array(
				'task_id' => $task['id'], 
				'project_id' => $task['project_id']
			)
	) ?>
<?php endif */ ?>

<?php if (isset($task['is_active']) && $this->projectRole->canChangeTaskStatusInColumn($task['project_id'], $task['column_id'])): ?>
	    <?php if ($this->task->taskMetadataModel->get($task['id'], 'KPI_moved') == true): ?>
	    	
	    	<span title="<?= t('KPI Moved') ?>" class="tooltip">
	    		
	    		<?= $this->url->icon('check-square-o', 'Moved from Week', 'KPIcontroller', 'editmoved', ['plugin' => 'KPI', 'task_id' => $task['id'], 'set' => false])?>
        		<?= $this->KPIHelper->GetWeek($task['date_creation']) ?>
        	</span>
		
	    <?php else: ?>
	    	
	    	<?= $this->url->icon('square-o', 'Mark as moved', 'KPIcontroller', 'editmoved', ['plugin' => 'KPI', 'task_id' => $task['id'], 'set' => true]) ?>
	    	
		<?php endif ?>
	    
<?php endif ?>

<?php /* if ($this->user->hasProjectAccess('TaskModificationController', 'edit', $task['project_id'])): ?>
		<?= $this->modal->confirm(
			'ban', 
			'', 
			'TaskCancellationController', 
			'cancel', 
			array(
				'plugin' => 'Instantactions', 
				'task_id' => $task['id'], 
				'project_id' => $task['project_id']
			)
		) ?>

<?php endif */ ?>








<style>
.fa.fa-times.fa-fw{
        color: gray;
}
.fa.fa-times.fa-fw:hover{ 
        color: black; 
}
  
.fa.fa-ban.fa-fw{
        color: gray;
}
.fa.fa-ban.fa-fw:hover{ 
        color: black; 
}
 
.fa.fa-edit.fa-fw{
        color: gray;
}
.fa.fa-edit.fa-fw:hover{ 
        color: black; 
}
 
.fa.fa-trash-o.fa-fw{
        color: gray;
}
.fa.fa-trash-o.fa-fw:hover{ 
        color: black; 
} 
</style>
