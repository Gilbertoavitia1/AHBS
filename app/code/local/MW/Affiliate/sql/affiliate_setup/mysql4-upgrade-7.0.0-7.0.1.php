<?php
$installer = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE `sales_flat_order` ADD COLUMN `affiliate_network` int(11) NULL DEFAULT '1';");

$installer->endSetup(); 