<?php
$installer = $this;
$installer->startSetup();

$installer->run("ALTER TABLE `mw_affiliate_customers` ADD COLUMN `anetwork` int(11) NOT NULL default 1;");

$installer->endSetup(); 