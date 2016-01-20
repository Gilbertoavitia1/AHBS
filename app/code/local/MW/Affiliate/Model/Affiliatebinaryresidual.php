<?php
class MW_Affiliate_Model_Affiliatebinaryresidual extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('affiliate/affiliatebinaryresidual');
    }
    
    public function saveResidual($data)
    {    
    	$write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $sql = "INSERT INTO mw_affiliate_binary_residual (start_date, end_date, customer_id, vp, vg, vpleft, vpright, cp, package, package_cp, aleft, aright) VALUES ("
        . "'{$data['start_date']}', '{$data['end_date']}', {$data['customer_id']}, {$data['vp']}, {$data['vg']}, {$data['left']}, {$data['right']}, {$data['cp']}, '{$data['package']}', '{$data['package_cp']}', '{$data['aleft']}', '{$data['aright']}')"; 
	
//        echo $sql.'<br>';
    	$write->query($sql);
    }
    
    public function updateResidual($data)
    {    
    	$write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $sql = "UPDATE mw_affiliate_binary_residual SET "
        . "vp = {$data['vp']}, "
        . "vg = {$data['vg']}, "
        . "vpleft = {$data['left']}, "
        . "vpright = {$data['right']}, "
        . "cp = {$data['cp']}, "
        . "package = '{$data['package']}', "
        . "package_cp = '{$data['package_cp']}', "
        . "aleft = '{$data['aleft']}', "
        . "aright = '{$data['aright']}' WHERE customer_id = {$data['customer_id']} and start_date = '{$data['start_date']}'"; 
	
//        echo $sql.'<br>';
    	$write->query($sql);
    }
	
}