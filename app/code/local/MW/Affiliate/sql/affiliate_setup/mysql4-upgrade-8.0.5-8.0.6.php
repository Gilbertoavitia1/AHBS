<?php
$installer = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE `mw_affiliate_binary_residual` ADD COLUMN `aleft` text NULL;
ALTER TABLE `mw_affiliate_binary_residual` ADD COLUMN `aright` text NULL;

");

$installer->endSetup(); 