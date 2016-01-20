<?php
class MW_Affiliate_Model_Affiliatebonus extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('affiliate/affiliatebonus');
    }
    
    public function saveBonus($data)
    {    
    	$collection = Mage::getModel('affiliate/affiliatebonus')->getCollection();
    	$write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $sql = "INSERT INTO {$collection->getTable('affiliatebonus')} (start_day, end_date, parent_id, child_id, total, website_id, bonus_id, package) VALUES ("
        . "'{$data['start_date']}', '{$data['end_date']}', {$data['parent_id']}, {$data['child_id']}, {$data['total']}, {$data['store_id']}, {$data['bonus_id']}, '{$data['package']}')"; 
	
    	$write->query($sql);
    }
	
}