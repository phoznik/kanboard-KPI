<?php

namespace Kanboard\Plugin\KPI\Helper;

use Kanboard\Core\Base;

class KPIHelper extends Base
{
    public function GetWeek($timestamp)
    {
        return intval(date('W', $timestamp));
    }
	
	public function GetPreviousColumnId($column_id)
	{
		$column = $this->columnModel->getById($column_id);
		return (int) $this->db->table(self::TABLE)->eq('project_id', $column['project_id'])->eq('position', $column['position'] - 1)->findOneColumn('id');
	}
}