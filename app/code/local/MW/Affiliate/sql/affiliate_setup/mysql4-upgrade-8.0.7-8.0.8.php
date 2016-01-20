<?php
$installer = $this;
$installer->startSetup();
$installer->run("ALTER TABLE `mw_affiliate_binary_preferred` ADD COLUMN `currency` VARCHAR(20) NULL;");
$installer->endSetup(); 