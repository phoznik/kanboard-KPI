<?php

namespace Kanboard\Plugin\KPI;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Translator;
use Kanboard\Plugin\KPI\Formatter\KPIFormatter;
use Kanboard\Plugin\KPI\Action\TestAction;
use Kanboard\Plugin\KPI\Action\ColumnWeeklyUpdate;
use Kanboard\Plugin\KPI\Action\AddWeekToTask;
# use Kanboard\Model\TaskMetadata;

class Plugin extends Base
{
    public function initialize()
    {
		$this->route->addRoute('KPI/:project_id', 'KPIController', 'show', 'plugin');
		$this->route->addRoute('KPI/:project_id/sort/:sorting', 'KPIController', 'show', 'plugin');
		
		# This might be where the links are defined
		$this->template->hook->attach('template:project-header:view-switcher', 'KPI:project_header/views');
		$this->template->hook->attach('template:project:dropdown', 'KPI:project/dropdown');
        
        #Add the javascript chart when the Core layout js runs
        #$this->hook->on('template:layout:js', array('template' => 'plugins/KPI/Assets/chart-tasks-time-comparison.js'));
        $this->hook->on('template:layout:js', array('template' => 'plugins/KPI/Assets/chart-project-KPI-hours.js'));
        $this->hook->on('template:layout:js', array('template' => 'plugins/KPI/Assets/chart-project-KPI-tasks.js'));
        $this->hook->on('template:layout:css', array('template' => 'plugins/KPI/Assets/KPI.css'));
		
		
		#$this->hook->on('model:task:creation', array($this, 'addWeekToTask'));

		#register new automatic actions
        $this->actionManager->register(new TestAction($this->container));
		$this->actionManager->register(new ColumnWeeklyUpdate($this->container));
		$this->actionManager->register(new AddWeekToTask($this->container));

		#Add the 'moved' checkbox on the board view
		#$this->template->hook->attach('template:board:task:footer', 'KPI:task_kpi/footer');
		$this->template->hook->attach('template:board:private:task:after-title', 'KPI:task_kpi/footer');

		#Register KPI helper class
		$this->helper->register('KPIHelper', '\Kanboard\Plugin\KPI\Helper\KPIHelper');

		/*$this->container['KPIFormatter'] = $this->container->factory(function ($c) {
            return new KPIFormatter($c);
        });*/
    }

    public function onStartup()
    {
        Translator::load($this->languageModel->getCurrentLanguage(), __DIR__.'/Locale');
    }

	public function addWeekToTask()
	{
		
	}

    public function getPluginName()
    {
        return 'KPI';
    }

    public function getPluginDescription()
    {
        return t('Display Linak Style KPIs');
    }

    public function getPluginAuthor()
    {
        return 'Daryl';
    }

    public function getPluginVersion()
    {
        return '1.0.0';
    }

    public function getPluginHomepage()
    {
        return 'https://github.com/kanboard/plugin-myplugin';
    }
}

