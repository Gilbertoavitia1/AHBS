<?php

$installer = $this;
$collection = Mage::getModel('affiliate/affiliatebanner')->getCollection();
$installer->startSetup();

$sql ="";
$sql .="ALTER TABLE {$collection->getTable('affiliategroup')} ADD `limit_day` int(11) NOT NULL DEFAULT '0' AFTER `group_name`;";
$sql .="ALTER TABLE {$collection->getTable('affiliategroup')} ADD `limit_order` int(11) NOT NULL DEFAULT '0' AFTER `limit_day`;";
$sql .="ALTER TABLE {$collection->getTable('affiliategroup')} ADD `limit_commission` double(15,0) NOT NULL DEFAULT '0' AFTER `limit_order`;";

$installer->run($sql);
$installer->endSetup();
