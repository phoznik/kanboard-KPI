<?php
namespace Kanboard\Plugin\KPI\Action;

use Kanboard\Action\Base;
use Kanboard\Model\TaskModel;
use Kanboard\Model\ColumnModel;

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
            TaskModel::EVENT_OPEN,
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
            #'color_id' => t('Color for past due tasks'),
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
        return array(
            'task_id',
            'task' => array(
            	'project_id',
            	'column_id',
			),
        );
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
    	$project_id = $data['task']['project_id'];
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
			#$this->logger->debug('KPI: current column: '.print_r($column, true));
					
			if($column['position'] > 2)  #only update the weekly columns
			{
				$this->db->table(ColumnModel::TABLE)->eq('id', $column['id'])->update(array(
            		'title' => t('Week %d', $wk_index),	# 'Week 'strval($wk_index),
            		'week' => $wk_index,
        		));
				
				$wk_index++;
				
				#find all tasks in this column
				$col_tasks = $this->db->table(TaskModel::TABLE)
	                ->columns('id', 'position')
	                ->eq('project_id', $project_id)
	                ->eq('column_id', $column['id'])
	                ->findAll();
				
				#find the id of the previous column
				$new_col = $this->db->table(ColumnModel::TABLE)
					->eq('project_id', $column['project_id'])
					->eq('position', $column['position'] - 1)
					->findOneColumn('id');
					
				foreach($col_tasks as $task)
				{
					$this->taskPositionModel->movePosition($project_id, $task['id'], $new_col, $task['position']);
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
		return date('w', time()) ==	'0';	#only update on Sundays 
    }
}