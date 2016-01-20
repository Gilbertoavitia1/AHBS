<?php

$installer = $this;
$collection = Mage::getModel('affiliate/affiliatebanner')->getCollection();
$installer->startSetup();

$pending = MW_Affiliate_Model_Status::PENDING;
$invoiced = MW_Affiliate_Model_Status::INVOICED;



$sql_history ="UPDATE  {$collection->getTable('affiliatehistory')}
				SET status =".$pending." where status = ".$invoiced.";";

$installer->run($sql_history);

$sql_transaction ="UPDATE  {$collection->getTable('affiliatetransaction')}
				SET status =".$pending." where status = ".$invoiced.";";

$installer->run($sql_transaction);

	
$installer->endSetup();
