<?php
$installer = $this;
$installer->startSetup();
$installer->run("ALTER TABLE `mw_affiliate_customers` ADD COLUMN `virtual_office` int(11) NULL;");
$installer->endSetup(); 