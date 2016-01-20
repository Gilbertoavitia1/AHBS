<?php

$installer = $this;
$collection = Mage::getModel('affiliate/affiliatebanner')->getCollection();
$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$collection->getTable('affiliatebannermember')};
CREATE TABLE {$collection->getTable('affiliatebannermember')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `banner_id` int(11) NOT NULL default '0',
  `customer_id` int(11) NOT NULL default '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$sql_banner ="";
$sql_banner .="ALTER TABLE {$collection->getTable('affiliatebanner')} ADD `group_id` varchar(255) NOT NULL DEFAULT '' AFTER `image_name`;";

$installer->run($sql_banner);

$sql ="";
$sql .="ALTER TABLE {$collection->getTable('affiliatecustomers')} ADD `bank_name` varchar(255) NOT NULL DEFAULT '' AFTER `referral_code`;";
$sql .="ALTER TABLE {$collection->getTable('affiliatecustomers')} ADD `name_account` varchar(255) NOT NULL DEFAULT '' AFTER `bank_name`;";
$sql .="ALTER TABLE {$collection->getTable('affiliatecustomers')} ADD `bank_country` varchar(255) NOT NULL DEFAULT '' AFTER `name_account`;";
$sql .="ALTER TABLE {$collection->getTable('affiliatecustomers')} ADD `swift_bic` varchar(255) NOT NULL DEFAULT '' AFTER `bank_country`;";
$sql .="ALTER TABLE {$collection->getTable('affiliatecustomers')} ADD `account_number` varchar(255) NOT NULL DEFAULT '' AFTER `swift_bic`;";
$sql .="ALTER TABLE {$collection->getTable('affiliatecustomers')} ADD `re_account_number` varchar(255) NOT NULL DEFAULT '' AFTER `account_number`;";
$sql .="ALTER TABLE {$collection->getTable('affiliatecustomers')} ADD `referral_site` varchar(255) NOT NULL DEFAULT '' AFTER `re_account_number`;";

$installer->run($sql);

$sql_transacation = "ALTER TABLE {$collection->getTable('affiliatetransaction')} ADD `show_customer_invited` int(11) NOT NULL default '0' AFTER `transaction_time`;";
 	
$installer->run($sql_transacation);

$sql_withdrawn ="";
$sql_withdrawn .="ALTER TABLE {$collection->getTable('affiliatewithdrawn')} ADD `payment_gateway` varchar(255) NOT NULL DEFAULT '' AFTER `customer_id`;";
$sql_withdrawn .="ALTER TABLE {$collection->getTable('affiliatewithdrawn')} ADD `payment_email` varchar(255) NOT NULL DEFAULT '' AFTER `payment_gateway`;";
$sql_withdrawn .="ALTER TABLE {$collection->getTable('affiliatewithdrawn')} ADD `bank_name` varchar(255) NOT NULL DEFAULT '' AFTER `payment_email`;";
$sql_withdrawn .="ALTER TABLE {$collection->getTable('affiliatewithdrawn')} ADD `name_account` varchar(255) NOT NULL DEFAULT '' AFTER `bank_name`;";
$sql_withdrawn .="ALTER TABLE {$collection->getTable('affiliatewithdrawn')} ADD `bank_country` varchar(255) NOT NULL DEFAULT '' AFTER `name_account`;";
$sql_withdrawn .="ALTER TABLE {$collection->getTable('affiliatewithdrawn')} ADD `swift_bic` varchar(255) NOT NULL DEFAULT '' AFTER `bank_country`;";
$sql_withdrawn .="ALTER TABLE {$collection->getTable('affiliatewithdrawn')} ADD `account_number` varchar(255) NOT NULL DEFAULT '' AFTER `swift_bic`;";
$sql_withdrawn .="ALTER TABLE {$collection->getTable('affiliatewithdrawn')} ADD `re_account_number` varchar(255) NOT NULL DEFAULT '' AFTER `account_number`;";

$installer->run($sql_withdrawn);

$affiliate_withdrawns = Mage::getModel('affiliate/affiliatewithdrawn')->getCollection();

if(sizeof($affiliate_withdrawns) > 0){
	foreach ($affiliate_withdrawns as $affiliate_withdrawn) {
		
		$customer_id = $affiliate_withdrawn ->getCustomerId();
		$withdrawn_id = $affiliate_withdrawn ->getWithdrawnId();
		$affiliate_customer = Mage::getModel('affiliate/affiliatecustomers')->load($customer_id);
		$payment_gateway = $affiliate_customer ->getPaymentGateway();
		$payment_email = $affiliate_customer ->getPaymentEmail();
		if($payment_gateway == 'banktransfer') $payment_email = '';
	    	
	  	$bank_name = $affiliate_customer->getBankName();
		$name_account = $affiliate_customer->getNameAccount();
		$bank_country = $affiliate_customer->getBankCountry();
		$swift_bic = $affiliate_customer->getSwiftBic();
		$account_number= $affiliate_customer->getAccountNumber();
		$re_account_number = $affiliate_customer->getReAccountNumber();
	  	
	  	$withdrawData =   array('payment_gateway'=>$payment_gateway,
	              				'payment_email'=>$payment_email,
	              				'bank_name'=>$bank_name,
					    	    'name_account'=>$name_account,
					    	    'bank_country'=>$bank_country,
					    	    'swift_bic'=>$swift_bic,
					    	    'account_number'=>$account_number,
					    	    're_account_number'=>$re_account_number);
  
	  	$affiliate_withdrawn ->setData($withdrawData)->setId($withdrawn_id)->save();
	}
	       
}
$installer->endSetup();
