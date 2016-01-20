<?php
// Generate a random character string
function rand_str($length = 32, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890')
{
    // Length of character list
    $chars_length = (strlen($chars) - 1);

    // Start our string
    $string = $chars{rand(0, $chars_length)};
   
    // Generate random string
    for ($i = 1; $i < $length; $i = strlen($string))
    {
        // Grab a random character from our list
        $r = $chars{rand(0, $chars_length)};
       
        // Make sure the same two characters don't appear next to each other
        if ($r != $string{$i - 1}) $string .=  $r;
    }
   
    // Return the string
    return $string;
}

$installer = $this;
$collection = Mage::getModel('affiliate/affiliatebanner')->getCollection();
$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$collection->getTable('affiliatecustomerprogram')};

DROP TABLE IF EXISTS {$collection->getTable('affiliategroup')};
CREATE TABLE {$collection->getTable('affiliategroup')} (
  `group_id` int(11) unsigned NOT NULL auto_increment,
  `group_name` varchar(255) NOT NULL default '',
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$collection->getTable('affiliategroupmember')};
CREATE TABLE {$collection->getTable('affiliategroupmember')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `group_id` int(11) NOT NULL default '0',
  `customer_id` int(11) NOT NULL default '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$collection->getTable('affiliategroupprogram')};
CREATE TABLE {$collection->getTable('affiliategroupprogram')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `group_id` int(11) NOT NULL default '0',
  `program_id` int(11) NOT NULL default '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 ");

$sql_member_new ="ALTER TABLE {$collection->getTable('affiliatecustomers')} CHANGE `payment_gateway` `payment_gateway` varchar(255) NOT NULL default ''; ";
$installer->run($sql_member_new);

$affiliate_customer_news = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
						->addFieldToFilter('payment_gateway', array('in' => array(1, 2)));
						
foreach ($affiliate_customer_news as $affiliate_customer_new) {
	
	$payment_gateway = $affiliate_customer_new->getPaymentGateway();
	if($payment_gateway == 1) $payment_gateway_new = PAYPAL;
	else if($payment_gateway == 2) $payment_gateway_new = MONEYBOOKERS;
	
	$sql_payment_gateway ="UPDATE  {$collection->getTable('affiliatecustomers')}
				SET payment_gateway="."'".$payment_gateway_new."'"." where payment_gateway = ".$payment_gateway.";";
	$installer->run($sql_payment_gateway);
}

$sql_member ="";
$sql_member .="ALTER TABLE {$collection->getTable('affiliatecustomers')} ADD `referral_code` varchar(255) NOT NULL default '' AFTER `total_paid`;";

$installer->run($sql_member);

$sql_group ="";
$sql_group .="INSERT INTO {$collection->getTable('affiliategroup')} (group_id, group_name)
				VALUES(1,'default group');";

$installer->run($sql_group);

$affiliate_customers = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
				->addFieldToFilter('active', MW_Affiliate_Model_Statusactive::ACTIVE);
				
foreach($affiliate_customers as $affiliate_customer)
{   
	$i = 0;
	$referral_code = rand_str(6);
	while ($i == 0) {
	       $affiliate_customers_filter = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
							                        ->addFieldToFilter('referral_code', $referral_code);
	       if(sizeof($affiliate_customers_filter) > 0){
		       	$i = 0;
		       	$referral_code = rand_str(6);
	       }else {
	       			$i = 1;
	       }  				        
	}
	$referral_code_new = $referral_code;
	$sql_referral_code = "";
	$sql_referral_code .="UPDATE  {$collection->getTable('affiliatecustomers')}
				SET referral_code="."'".$referral_code_new."'"." where customer_id = ".$affiliate_customer->getCustomerId().";";
	$installer->run($sql_referral_code);	
		
}

$sql ="";
foreach($affiliate_customers as $affiliate_customer)
{
	$sql .="INSERT INTO {$collection->getTable('affiliategroupmember')} (customer_id,group_id)
				VALUES(".$affiliate_customer->getCustomerId().",1);";
}

$installer->run($sql);
$installer->endSetup();
