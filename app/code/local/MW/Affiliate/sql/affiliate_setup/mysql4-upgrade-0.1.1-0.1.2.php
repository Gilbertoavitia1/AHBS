<?php
$installer = $this;
$collection = Mage::getModel('affiliate/affiliatebanner')->getCollection();
$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$collection->getTable('affiliatecategoryprogram')};
DROP TABLE IF EXISTS {$collection->getTable('affiliateproductprogram')};

 ");

$sql_program ="";
$sql_program .="ALTER TABLE {$collection->getTable('affiliateprogram')} ADD `description` text NOT NULL default '' AFTER `program_name`;";
$sql_program .="ALTER TABLE {$collection->getTable('affiliateprogram')} ADD `conditions_serialized` mediumtext NOT NULL default '' AFTER `description`;";
$sql_program .="ALTER TABLE {$collection->getTable('affiliateprogram')} ADD `actions_serialized` mediumtext NOT NULL default '' AFTER `conditions_serialized`;";

$installer->run($sql_program);

$affiliate_programs = Mage::getModel('affiliate/affiliateprogram')->getCollection();
foreach($affiliate_programs as $affiliate_program)
{
	$program_id = $affiliate_program ->getProgramId();
	Mage::getModel('affiliate/affiliateprogram')->load($program_id) ->delete();
}

$sql_member ="";
$sql_member .="ALTER TABLE {$collection->getTable('affiliatecustomers')} ADD `customer_time` datetime NULL AFTER `customer_invited`;";

$installer->run($sql_member);

$affiliate_customers = Mage::getModel('affiliate/affiliatecustomers')->getCollection();


$sql ="";
foreach($affiliate_customers as $affiliate_customer)
{
	$sql .="UPDATE  {$collection->getTable('affiliatecustomers')}
				SET customer_time="."'".now()."'"." where customer_id = ".$affiliate_customer->getCustomerId().";";
}

$installer->run($sql);
$installer->endSetup();
