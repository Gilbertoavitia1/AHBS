<?php
$installer = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE `mw_affiliate_binary_residual` ADD COLUMN `tleft` int(11) NULL;
ALTER TABLE `mw_affiliate_binary_residual` ADD COLUMN `tright` int(11) NULL;
ALTER TABLE `mw_affiliate_binary_residual` ADD COLUMN `tvg` int(11) NULL;
");

$installer->endSetup(); 