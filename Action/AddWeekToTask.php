<?php
namespace Kanboard\Plugin\KPI\Action;

use Kanboard\Model\TaskModel;
use Kanboard\Action\Base;
/**
 * Add week number to a task when it's created
 *
 * @package action
 * @author  Daryl Sielaff
 */
class AddWeekToTask extends Base
{
    /**
     * Get automatic action description
     *
     * @access public
     * @return string
     */
    public function getDescription()
    {
        return t('KPI: Add week number to task when its created.');
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
            TaskModel::EVENT_CREATE,
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
            #'column_id' => t('Column'),
        );
    }
    /**
     * Get the required parameter for the event
	 *   This is passed as $data to the two functions below?
     *
     * @access public
     * @return string[]
     */
    public function getEventRequiredParameters()
    {
        return array(
            'task_id',
            'task' => array(
            	'date_creation',
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
    	$column = $this->columnModel->getById($data['task']['column_id']);
    	$wk_num = $column['week'];
		#$this->logger->debug('KPI: '.print_r($data, true));
		#$this->logger->debug('KPI: '.print_r($column, true));
        
		return $this->taskMetadataModel->save($data['task_id'], ['KPI_wk_created' => $wk_num]);
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
        return true;
    }
}