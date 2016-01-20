<?php
$installer = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE `mw_affiliate_customers` DROP COLUMN `affiliate_type`;
ALTER TABLE `mw_affiliate_customers` DROP COLUMN `anetwork`;
ALTER TABLE `mw_affiliate_customers` ADD COLUMN `anetwork` int(11) NULL;");

$installer->endSetup(); 