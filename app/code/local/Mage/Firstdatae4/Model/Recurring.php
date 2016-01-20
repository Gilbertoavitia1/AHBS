<?php
class Mage_Firstdatae4_Model_Recurring extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('firstdatae4/recurring');
    }
    
    public function recurring_data($customerData){
    	$write = Mage::getSingleton('core/resource')->getConnection('core_write');
    	$sql = 'INSERT INTO firstdata_recurring(order_id,customer_id,address_id,type,created_date,updated_date,next_date) 
    	VALUES('.$customerData['order_id'].','
                .$customerData['customer_id'].","
                .$customerData['address_id'].",'"
                .$customerData['type']."','"
                .$customerData['created_date']."','"
                .$customerData['updated_date']."','"
                .$customerData['next_date']."')";
    	//var_dump($sql);exit;
    	$write->query($sql);
    }    
    
    public function processing_data($id){
        $updated = date('Y-m-d H:i:s');
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $status = "PROCESSING";
        $sql = "UPDATE firstdata_recurring SET updated_date='{$updated}', status = '{$status}' WHERE order_id={$id}";
    	$write->query($sql);
    }
    
    public function nextRun($id, $next){
        $updated = date('Y-m-d H:i:s');
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $status = "RUNNING";
        $sql = "UPDATE firstdata_recurring SET updated_date='{$updated}', next_date = '{$next}', status = '{$status}' WHERE order_id={$id}";
    	$write->query($sql);
    }
    
    public function deleteRecurring($id){
        $updated = date('Y-m-d H:i:s');
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $status = "DELETED";
        $sql = "UPDATE firstdata_recurring SET status = '{$status}', updated_date='{$updated}' WHERE recurring_id={$id}";
    	$write->query($sql);
    }
    
    public function prepareHistory($customerData){
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
    	$sql = 'INSERT INTO firstdata_recurring_history(recurring_id, date_of_run, order_created) 
    	VALUES('.$customerData['recurring_id'].",'"
                .$customerData['date_of_run']."','"
                .$customerData['order_created']."')";
    	//var_dump($sql);exit;
    	$write->query($sql);        
    }   
    
	
}