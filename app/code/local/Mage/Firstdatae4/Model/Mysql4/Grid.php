<?php

class Mage_Firstdatae4_Model_Mysql4_Grid extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		$this->_init('firstdata/grid', 'recurring_id');
	}
}