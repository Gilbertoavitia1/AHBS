<?php
$installer = $this;
$collection = Mage::getModel('affiliate/affiliatebanner')->getCollection();
$installer->startSetup();

$sql_program ="";
$sql_program .="ALTER TABLE {$collection->getTable('affiliateprogram')} ADD `store_view` varchar(255) NOT NULL DEFAULT '0' AFTER `program_position` ;";

$installer->run($sql_program);

$sql_banner ="";
$sql_banner .="ALTER TABLE {$collection->getTable('affiliatebanner')} ADD `store_view` varchar(255) NOT NULL DEFAULT '0' AFTER `image_name`;";

$installer->run($sql_banner);


$installer->endSetup();
