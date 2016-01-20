<?php
class MW_Affiliate_Model_Affiliateproductprogram extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('affiliate/affiliateproductprogram');
    }
	public function saveProductProgram($orderData)
    {
    	$collection = Mage::getModel('affiliate/affiliateproductprogram')->getCollection();
    	$write = Mage::getSingleton('core/resource')->getConnection('core_write');
    	$sql = 'INSERT INTO '.$collection->getTable('affiliateproductprogram').' (product_id,program_id)
    				VALUES('.$orderData['product_id'].",'".$orderData['program_id']."')";
    	//echo $sql;exit;
		$write->query($sql);
    }
	
}