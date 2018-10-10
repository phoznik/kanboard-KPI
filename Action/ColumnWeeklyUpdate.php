<?php
namespace Kanboard\Plugin\KPI\Action;

use Kanboard\Action\Base;
use Kanboard\Model\TaskModel;
use Kanboard\Model\ColumnModel;
#use Kanboard\Core\Base;

/**
 * Shift all columns over (update week numbers and move all tasks)
 *
 * @package action
 * @author  Daryl Sielaff
 */
class ColumnWeeklyUpdate extends Base
{
	/**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'columns';
	
    /**
     * Get automatic action description
     *
     * @access public
     * @return string
     */
    public function getDescription()
    {
        return t('KPI: Update column week numbers and move all tasks.');
    }
    /**
     * Get the list of compatible events
     *
     * @access public
     * @return array
     */
    public function getCompatibleEvents()
    {
        return array(
            TaskModel::EVENT_DAILY_CRONJOB,
        );
    }
    /**
     * Get the required parameter for the action (defined by the user)
     *
     * @access public
     * @return array
     */
    public function getActionRequiredParameters()
    {
        return array(
        );
    }
    /**
     * Get the required parameter for the event
     *
     * @access public
     * @return string[]
     */
    public function getEventRequiredParameters()
    {
        return array('tasks');
        /*    'task_id',
            'task' => array(
            	'project_id',
            	'column_id',
			),
        );*/
    }
    /**
     * Execute the action
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool            True if the action was executed or false when not executed
     */
    public function doAction(array $data)
    {
    	#$project_id = $data['task']['project_id'];
    	$project_id = $data['project_id'];
    	$columns = $this->columnModel->getAll($project_id); #get all columns in this project
    	#$tasks = $this->taskFinderModel->getAll($project_id); #get all tasks in this project
    	$current_week = $this->helper->KPIHelper->GetWeek(time());
		
		$this->logger->debug('KPI: data '.print_r($data, true));
		$this->logger->debug('KPI: columns '.print_r($columns, true));
		#$this->logger->debug('KPI: tasks '.print_r($tasks, true));
		$this->logger->debug('KPI: current week: '.$current_week);
		
		/** column numbers
		 * 1: Done
		 * 2: Past Due
		 * 3: Current Week
		 * 4: Current Week + 1
		 * 5: Current Week + 2
		 * 6: Current Week + 3
		**/
		$wk_index = $current_week;
		foreach ($columns as $column)
		{
			$this->logger->debug('--------KPI: wk_index: '.$wk_index);
			$this->logger->debug('KPI: current column: '.print_r($column, true));
			
			if($column['position'] == 2) #move closed tasks from 'past due' to 'done'
			{
				#find all closed tasks in this column
				$col_tasks = $this->db->table(TaskModel::TABLE)
	                		->columns('id', 'position')
		                	->eq('project_id', $project_id)
		                	->eq('column_id', $column['id'])
					->eq('is_active', 0)
	        	        	->findAll();
				
				#find the id of the column to the left
				$new_col = $this->db->table(ColumnModel::TABLE)
					->eq('project_id', $column['project_id'])
					->eq('position', $column['position'] - 1)
					->findOneColumn('id');
					
				foreach($col_tasks as $task)
				{
					$retval = $this->taskPositionModel->movePosition($project_id, $task['id'], $new_col, $task['position'], 0, false, false);
					$this->logger->debug("KPI: updated past due task:{$retval} :#{$task['id']}: new col:{$new_col}");
				}
			}
			else if($column['position'] > 2)  #only update the weekly columns
			{
				$retval = $this->db->table('columns')->eq('id', $column['id'])->update(array(
            				'title' => "Week {$wk_index}",  #t('Week %d', $wk_index),
            				'week' => intval($wk_index),
        				));
				
				$this->logger->debug('KPI: retval: '.$retval);
	
				$wk_index++;
			
				#find all tasks in this column
				$col_tasks = $this->db->table(TaskModel::TABLE)
	                		->columns('id', 'position')
		                	->eq('project_id', $project_id)
		                	->eq('column_id', $column['id'])
	        	        	->findAll();
				
				#find the id of the column to the left
				$new_col = $this->db->table(ColumnModel::TABLE)
					->eq('project_id', $column['project_id'])
					->eq('position', $column['position'] - 1)
					->findOneColumn('id');
					
				foreach($col_tasks as $task)
				{
					$retval = $this->taskPositionModel->movePosition($project_id, $task['id'], $new_col, $task['position'], 0, false, false);
					$this->logger->debug("KPI: updated task:{$retval} :#{$task['id']}: new col:{$new_col}");
				}
			}
		}
		
        return true;
    }
    /**
     * Check if the event data meet the action condition
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool
     */
    public function hasRequiredCondition(array $data)
    {
    	#return true;
	#return date('w', time()) == '0';	#only update on Sundays 
	return date('w', time()) == '1';	#only update on Mondays 
	#return date('w', time()) == '2';	#only update on Tuesdays 
	#return date('w', time()) == '3';	#only update on Wednesdays
	#return date('w', time()) == '4';	#only update on Thursdays
	#return date('w', time()) == '5';	#only update on Fridays
    }
}
