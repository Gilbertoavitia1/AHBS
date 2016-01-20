<?php
$installer = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE `mw_affiliate_binary_residual` DROP COLUMN `left`;
ALTER TABLE `mw_affiliate_binary_residual` DROP COLUMN `right`;
ALTER TABLE `mw_affiliate_binary_residual` ADD COLUMN `vpleft` int(11) NULL;
ALTER TABLE `mw_affiliate_binary_residual` ADD COLUMN `vpright` int(11) NULL;
");

$installer->endSetup(); 