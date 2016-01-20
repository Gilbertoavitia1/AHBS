<?php

$installer = $this;
$collection = Mage::getModel('affiliate/affiliatebanner')->getCollection();
$installer->startSetup();

$sql_customer ="ALTER TABLE {$collection->getTable('affiliatecustomers')} ADD `invitation_type` int(2) NOT NULL DEFAULT '0' AFTER `customer_invited`;";

$installer->run($sql_customer);

$invitation_type_value = MW_Affiliate_Model_Typeinvitation::REFERRAL_LINK;
$sql_customer_new ="UPDATE  {$collection->getTable('affiliatecustomers')}
				SET invitation_type =".$invitation_type_value." where customer_invited != 0 ;";

$installer->run($sql_customer_new);

$sql_transaction ="ALTER TABLE {$collection->getTable('affiliatetransaction')} ADD `invitation_type` int(2) NOT NULL DEFAULT '1' AFTER `customer_invited`;";

$installer->run($sql_transaction);

$sql_history ="ALTER TABLE {$collection->getTable('affiliatehistory')} ADD `invitation_type` int(2) NOT NULL DEFAULT '1' AFTER `customer_invited`;";

$installer->run($sql_history);


$sql_inviation ="";
$sql_inviation .="ALTER TABLE {$collection->getTable('affiliateinvitation')} ADD `count_click_link` int(11) NOT NULL DEFAULT '0' AFTER `ip`;";
$sql_inviation .="ALTER TABLE {$collection->getTable('affiliateinvitation')} ADD `count_register` int(11) NOT NULL DEFAULT '0' AFTER `count_click_link`;";
$sql_inviation .="ALTER TABLE {$collection->getTable('affiliateinvitation')} ADD `count_purchase` int(11) NOT NULL DEFAULT '0' AFTER `count_register`;";
$sql_inviation .="ALTER TABLE {$collection->getTable('affiliateinvitation')} ADD `referral_from` varchar(255) NOT NULL DEFAULT '' AFTER `count_purchase`;";
$sql_inviation .="ALTER TABLE {$collection->getTable('affiliateinvitation')} ADD `referral_from_domain` varchar(255) NOT NULL DEFAULT '' AFTER `referral_from`;";
$sql_inviation .="ALTER TABLE {$collection->getTable('affiliateinvitation')} ADD `referral_to` varchar(255) NOT NULL DEFAULT '' AFTER `referral_from_domain`;";
$sql_inviation .="ALTER TABLE {$collection->getTable('affiliateinvitation')} ADD `order_id` varchar(255) NOT NULL DEFAULT '' AFTER `referral_to`;";
$sql_inviation .="ALTER TABLE {$collection->getTable('affiliateinvitation')} ADD `invitation_type` int(2) NOT NULL DEFAULT '1' AFTER `order_id`;";

$installer->run($sql_inviation);

$count_value = 1;
$click_lick = MW_Affiliate_Model_Statusinvitation::CLICKLINK;
$register = MW_Affiliate_Model_Statusinvitation::REGISTER;
$purchase = MW_Affiliate_Model_Statusinvitation::PURCHASE;

$sql_click_link ="UPDATE  {$collection->getTable('affiliateinvitation')}
				SET count_click_link =".$count_value." where status = ".$click_lick.";";

$installer->run($sql_click_link);

$sql_register ="UPDATE  {$collection->getTable('affiliateinvitation')}
				SET count_register =".$count_value." where status = ".$register.";";

$installer->run($sql_register);

$sql_purchase ="UPDATE  {$collection->getTable('affiliateinvitation')}
				SET count_purchase =".$count_value." where status = ".$purchase.";";

$installer->run($sql_purchase);
	
$installer->endSetup();
