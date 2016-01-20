<?php
$installer = $this;
$installer->startSetup();

$installer->run("ALTER TABLE `mw_affiliate_customers` ADD COLUMN `affiliate_type` int(11) NOT NULL default 1;");

$installer->endSetup(); 