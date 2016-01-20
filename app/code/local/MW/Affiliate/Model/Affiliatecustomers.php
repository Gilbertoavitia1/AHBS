<?php
class MW_Affiliate_Model_Affiliatecustomers extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('affiliate/affiliatecustomers');
    }
    
    public function saveCustomerAccount($customerData)
    {    
        $customerData['anetwork'] = 2;
    	$collection = Mage::getModel('affiliate/affiliatecustomers')->getCollection();
    	$write = Mage::getSingleton('core/resource')->getConnection('core_write');
        //if (!($customerData['anetwork'])) $customerData['anetwork'] = 1;
    	$sql = 'INSERT INTO '.$collection->getTable('affiliatecustomers').'(customer_id,active,payment_gateway,auto_withdrawn,invitation_type,withdrawn_level,reserve_level,total_commission,total_paid,customer_invited,status,customer_url,customer_photo, referral_sponsor, referral_code,
    	customer_time,payment_email,bank_name,name_account,bank_country,swift_bic,account_number,re_account_number,referral_site,autoapprove,anetwork) 
    	VALUES('.$customerData['customer_id'].','.$customerData['active'].",'".$customerData['payment_gateway']."',".$customerData['auto_withdrawn'].','.$customerData['invitation_type'].','.$customerData['withdrawn_level'].','.$customerData['reserve_level']
    	.','.$customerData['total_commission'].','.$customerData['total_paid'].','.$customerData['customer_invited'].','.$customerData['status'].",'".$customerData['customer_url']."','".$customerData['customer_photo']."','".$customerData['referral_sponsor']."','".$customerData['referral_code']."','".$customerData['customer_time']
    	."','".$customerData['payment_email']."','".$customerData['bank_name']."','".$customerData['name_account']."','".$customerData['bank_country']."','".$customerData['swift_bic']."','".$customerData['account_number']
    	."','".$customerData['re_account_number']."','".$customerData['referral_site']."',".$customerData['auto_approve'].",".$customerData['anetwork'].")";
        
//        echo "<pre>";print_R($customerData);
//    	echo $sql;//var_dump($sql);exit;
    	$write->query($sql);
    }
    
    public function updateCustomerApproval($customer_id, $type)
    {    
    	$collection = Mage::getModel('affiliate/affiliatecustomers')->getCollection();
    	$write = Mage::getSingleton('core/resource')->getConnection('core_write');
    	$sql = 'UPDATE '.$collection->getTable('affiliatecustomers').' SET `active` = '.$type.' WHERE `customer_id`='.$customer_id;
    	//var_dump($sql);exit;
    	$write->query($sql);
    }
	
}