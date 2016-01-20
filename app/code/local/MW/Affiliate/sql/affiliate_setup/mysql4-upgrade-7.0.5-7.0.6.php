<?php
$installer = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE `mw_affiliate_binary_residual` MODIFY COLUMN `vp` int(11);
ALTER TABLE `mw_affiliate_binary_residual` MODIFY COLUMN `vg` int(11);
ALTER TABLE `mw_affiliate_binary_residual` MODIFY COLUMN `vpleft` int(11);
ALTER TABLE `mw_affiliate_binary_residual` MODIFY COLUMN `vpright` int(11);
");

$installer->endSetup(); 