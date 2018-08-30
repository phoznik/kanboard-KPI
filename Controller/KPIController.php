<?php

namespace Kanboard\Plugin\KPI\Controller;

use Kanboard\Controller\BaseController;
use Kanboard\Filter\TaskProjectFilter;
use Kanboard\Model\TaskModel;

/**
 * Tasks KPI Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 * @property \Kanboard\Plugin\KPI\Formatter\KPIFormatter $KPIFormatter
 */
class KPIController extends BaseController
{
    /**
     * Show KPI charts for one project
     */
    public function show()
    {
        $project = $this->getProject();
        $search = $this->helper->projectHeader->getSearchQuery($project);
        $sorting = $this->request->getStringParam('sorting', '');
        $filter = $this->taskLexer->build($search)->withFilter(new TaskProjectFilter($project['id']));

        /*if($sorting === '') {
          $sorting = $this->configModel->get('gantt_task_sort', 'board');
        }*/

        if ($sorting === 'date') {
            $filter->getQuery()->asc(TaskModel::TABLE.'.date_started')->asc(TaskModel::TABLE.'.date_creation');
        } else {
            $filter->getQuery()->asc('column_position')->asc(TaskModel::TABLE.'.position');
        }

        $this->response->html($this->helper->layout->app('KPI:task_kpi/show', array(
            'project' => $project,
            'title' => $project['name'],
            'description' => $this->helper->projectHeader->getDescription($project),
            'sorting' => $sorting,
            'tasks' => $filter->format($this->taskGanttFormatter),
            'metrics' => $this->build($project['id']),
        )));
    }

	/**
     * Build report
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @return array
     */
    public function build($project_id)
    {
        $metrics = array();
        $total = 0;
        $columns = $this->columnModel->getAll($project_id);
        #echo '<pre>columns '; print_r($columns); echo '</pre>';

        foreach ($columns as $column) {
            #echo '<pre>column '; print_r($column); echo '</pre>';

            $nb_tasks = $this->taskFinderModel->countByColumnId($project_id, $column['id']);
            #echo '<pre>nb_tasks '; print_r($nb_tasks); echo '</pre>';

            $all_tasks_table = $this->db->table(TaskModel::TABLE)
                #->columns('SUM(time_estimated) AS time_estimated', 'SUM(time_spent) AS time_spent', 'is_active')
                ->columns('id', 'time_estimated', 'time_spent', 'is_active')
                ->eq('project_id', $project_id)
                ->eq('column_id', $column['id'])
                #->groupBy('is_active')
                ->findAll();

            #echo '<pre>all_tasks_table '; print_r($all_tasks_table); echo '</pre>';
            
            $total += $nb_tasks;

            #$rows = $this->db->table(TaskModel::TABLE)
            #    ->columns('SUM(time_estimated) AS time_estimated', 'SUM(time_spent) AS time_spent', 'is_active')
            #    ->eq('project_id', $project_id)
            #    ->groupBy('is_active')
            #    ->findAll();
            
            $nb_hrs_used = 0;
            $nb_hrs_plan = 0;
            $nb_task_complete = 0;
            $nb_task_plan = 0;
            $nb_tasks = 0;
            $nb_tasks_complete = 0;
            $nb_escalations = 0;

            #foreach ($rows as $row) {
            #    $nb_hrs_used += (float) $row['time_spent'];
            #    $nb_hrs_est += (float) $row['time_estimated'];
            #}

            foreach ($all_tasks_table as $task) {
                $nb_hrs_used += (float) $task['time_spent'];
                $nb_hrs_plan += (float) $task['time_estimated'];
                
                $tags = $this->taskTagModel->getTagsByTask($task['id']);
                #echo '<pre>tags '; print_r($tags); echo '</pre>';
                if (!empty($tags)){
                    $nb_escalations += 1;
                }

                $nb_tasks += 1;
                if($task['is_active'] == 0) {
                    $nb_tasks_complete += 1;
                }

            }
            
            $metrics[] = array(
                'column_title' => $column['title'],
                'nb_tasks' => $nb_tasks,
                'nb_hrs_used' => $nb_hrs_used,
                'nb_hrs_plan' => $nb_hrs_plan,
                'nb_tasks' => $nb_tasks,
                'nb_tasks_complete' => $nb_tasks_complete,
                'nb_escalations' => $nb_escalations
            );
        }

        if ($total === 0) {
            return array();
        }

        foreach ($metrics as &$metric) {
            if ($metric['nb_hrs_plan'] > 0){
                $metric['nb_hrs_percent'] = round(($metric['nb_hrs_used'] / $metric['nb_hrs_plan']) * 100, 0);
            }
            else{
                $metric['nb_hrs_percent'] = 100;
            }

            if($metric['nb_tasks'] > 0){
                $metric['nb_tasks_percent'] = round(($metric['nb_tasks_complete'] / $metric['nb_tasks']) * 100, 0);
            }
            else{
                $metric['nb_tasks_percent'] = 100;
            }
        }

        #echo '<pre>metrics '; print_r($metrics); echo '</pre>';

        return $metrics;
    }
	
    /**
     * Save new task start date and due date
     */
    public function save()
    {
        $this->getProject();
        $changes = $this->request->getJson();
        $values = [];

        if (! empty($changes['start'])) {
            $values['date_started'] = strtotime($changes['start']);
        }

        if (! empty($changes['end'])) {
            $values['date_due'] = strtotime($changes['end']);
        }

        if (! empty($values)) {
            $values['id'] = $changes['id'];
            $result = $this->taskModificationModel->update($values);

            if (! $result) {
                $this->response->json(array('message' => 'Unable to save task'), 400);
            } else {
                $this->response->json(array('message' => 'OK'), 201);
            }
        } else {
            $this->response->json(array('message' => 'Ignored'), 200);
        }
    }
	
	public function editmoved()
	{
	#	$template = 'KPI:moved';
	#	$success_message = 'Task is now considered moved.';
	#	$failure_message = 'Unable to mark this task as moved.';

	#	$project = $this -> getProject();
		$task = $this -> getTask();
		$key = $this->request->getStringParam('set');
		
	#	if ($this -> request -> getStringParam('confirmation') === 'yes') { #do this after user has confirmed yes
			
		
			#$values = $this->request->getValues();
			
			$this->taskMetadataModel->save($task['id'], ['KPI_moved' => $key]);
			#$this->taskMetadataModel->save($task['id'], ['KPI_moved' => false]);
			
			#reload the board to show the new checked/unchecked box
			#$this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])), true);
			$this->response->redirect($this->helper->url->to('BoardViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])), true);
	#	}
	#	else {	#generate the modal box
	#		$this -> response -> html($this -> template -> render($template, array('task' => $task, )));
	#	}
	}
}
