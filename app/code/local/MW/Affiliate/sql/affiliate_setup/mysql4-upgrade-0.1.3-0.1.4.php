<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();

$sql ="";
$sql .="ALTER TABLE {$resource->getTableName('sales/quote_item')} ADD `mw_affiliate_discount` decimal(12,4) NULL DEFAULT '0.0000' AFTER `base_discount_amount` ;";
$sql .="ALTER TABLE {$resource->getTableName('sales/quote_item')} ADD `mw_credit_discount` decimal(12,4) NULL DEFAULT '0.0000' AFTER `mw_affiliate_discount` ;";

$installer->run($sql);

$installer->endSetup();
